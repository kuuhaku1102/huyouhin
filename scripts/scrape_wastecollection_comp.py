#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
不用品回収センター 比較ページ（wastecollection-comp）スクレイピングスクリプト
全ページの業者データを収集してExcel出力
"""

import asyncio
from typing import Dict, List
from urllib.parse import urljoin

import pandas as pd
from playwright.async_api import async_playwright

BASE_URL = "https://fuyouhincenter.jp/wastecollection-comp/"
OUTPUT_FILE = "fuyouhincenter_wastecollection_comp.xlsx"


async def clean_text(element) -> str:
    if not element:
        return ""
    text = await element.inner_text()
    return " ".join(text.split())


async def get_attr(element, attr: str) -> str:
    if not element:
        return ""
    value = await element.get_attribute(attr)
    return value or ""


async def extract_table_values(company_el) -> Dict[str, str]:
    services: List[str] = []
    areas: List[str] = []
    prices: List[str] = []

    rows = await company_el.query_selector_all("table.gyosha_results_table tr")
    for row in rows:
        header_el = await row.query_selector("th")
        label = await clean_text(header_el)
        if not label:
            continue

        if "サービス内容" in label:
            items = await row.query_selector_all("li")
            services = [await clean_text(item) for item in items if await clean_text(item)]
        elif "対応エリア" in label:
            area_items = await row.query_selector_all(".area-table")
            if area_items:
                areas = [await clean_text(item) for item in area_items if await clean_text(item)]
            else:
                area_text = await clean_text(await row.query_selector("li"))
                if area_text:
                    areas = [area.strip() for area in area_text.replace("｜", ",").split(",") if area.strip()]
        elif "料金" in label:
            items = await row.query_selector_all("li")
            prices = [await clean_text(item) for item in items if await clean_text(item)]

    return {
        "サービス内容": " / ".join(services),
        "対応エリア": " / ".join(areas),
        "料金": " / ".join(prices),
    }


async def extract_company_data(company_el, page_number: int, rank: int) -> Dict[str, str]:
    name_el = await company_el.query_selector(".gyosha_results_ttl a")
    image_el = await company_el.query_selector(".gyosha_results_img img")
    rating_star_el = await company_el.query_selector(".star5_rating")

    table_values = await extract_table_values(company_el)
    company_url = await get_attr(name_el, "href")

    review_title_el = await company_el.query_selector(".gyosha_results_box_ttl")
    review_user_el = await company_el.query_selector(".reviews_box_name")
    review_rating_el = await company_el.query_selector(".reviews_star .rating_text")
    review_breakdown_el = await company_el.query_selector(".reviews_fee")
    review_text_el = await company_el.query_selector(".gyosha_results_box_text")
    review_image_el = await company_el.query_selector(".reviews_box_img img")

    return {
        "ページ": page_number,
        "順位": rank,
        "会社ID": await get_attr(company_el, "id"),
        "不用品回収業者名": await clean_text(name_el),
        "会社URL": company_url,
        "ロゴ画像URL": await get_attr(image_el, "src"),
        "ロゴ画像ALT": await get_attr(image_el, "alt"),
        "総合評価（星）": await get_attr(rating_star_el, "data-rate"),
        "総合評価（テキスト）": await clean_text(await company_el.query_selector(".gyosha_results_star_text")),
        "口コミ件数": await clean_text(await company_el.query_selector(".gyosha_results_star_text_num")),
        "概要": await clean_text(await company_el.query_selector(".gyosha_results_text")),
        **table_values,
        "口コミタイトル": await clean_text(review_title_el),
        "口コミユーザー": await clean_text(review_user_el),
        "口コミ評価": await clean_text(review_rating_el),
        "口コミ内訳": await clean_text(review_breakdown_el),
        "口コミ本文": await clean_text(review_text_el),
        "口コミ画像URL": await get_attr(review_image_el, "src"),
    }


async def extract_company_detail(page, company_url: str) -> Dict[str, str]:
    if not company_url:
        return {}

    detail_url = urljoin(BASE_URL, company_url)

    try:
        await page.goto(detail_url, wait_until="networkidle", timeout=60000)
    except Exception as exc:
        print(f"  ✗ 詳細ページ取得失敗: {detail_url} ({exc})")
        return {"詳細ページURL": detail_url}

    await page.wait_for_timeout(1500)

    detail_data: Dict[str, str] = {
        "詳細ページURL": detail_url,
    }

    container = await page.query_selector(".c_list_container")
    if not container:
        return detail_data

    detail_image_el = await container.query_selector(".c_list_img img")
    detail_data.update(
        {
            "詳細ページロゴ画像URL": await get_attr(detail_image_el, "src"),
            "詳細ページロゴ画像ALT": await get_attr(detail_image_el, "alt"),
        }
    )

    reviews_block = await container.query_selector(".c_list_contents_reviews")
    if reviews_block:
        overall_star_el = await reviews_block.query_selector(".c_list_rank .star5_rating")
        overall_rating_el = await reviews_block.query_selector(".c_list_rank .rating_text")
        review_count_el = await reviews_block.query_selector(".reviews_fee_num")

        detail_data.update(
            {
                "詳細ページ総合評価（星）": await get_attr(overall_star_el, "data-rate"),
                "詳細ページ総合評価（テキスト）": await clean_text(overall_rating_el),
                "詳細ページ口コミ件数": await clean_text(review_count_el),
            }
        )

        rating_rows = await reviews_block.query_selector_all(".c_list_reviews")
        for row in rating_rows:
            label = await clean_text(await row.query_selector(".c_list_reviews_text"))
            value = await clean_text(await row.query_selector(".gyosha_results_star_text"))
            if label and value:
                detail_data[f"詳細ページ{label}"] = value

    points_items = await container.query_selector_all(".c_list_point_area .c_list_point_item")
    if points_items:
        points = [await clean_text(item) for item in points_items if await clean_text(item)]
        detail_data["おすすめポイント"] = " / ".join(points)

    feature_text_el = await container.query_selector(".outline-company-feature p")
    detail_data["特徴"] = await clean_text(feature_text_el)

    company_section = await page.query_selector("#company")
    if company_section:
        rows = await company_section.query_selector_all("table.c_list_company_table tr")
        for row in rows:
            label = await clean_text(await row.query_selector("th"))
            if "公式ホームページ" in label:
                link_el = await row.query_selector("a")
                detail_data["公式ホームページURL"] = await get_attr(link_el, "href")

    return detail_data


async def scrape_all_pages() -> List[Dict[str, str]]:
    results: List[Dict[str, str]] = []

    async with async_playwright() as p:
        browser = await p.chromium.launch(
            headless=True,
            args=["--no-sandbox", "--disable-setuid-sandbox"],
        )
        context = await browser.new_context(
            user_agent=(
                "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
                "AppleWebKit/537.36 (KHTML, like Gecko) "
                "Chrome/120.0.0.0 Safari/537.36"
            )
        )
        page = await context.new_page()

        page_number = 1
        while True:
            url = BASE_URL if page_number == 1 else f"{BASE_URL}page/{page_number}/"
            print(f"スクレイピング中: {url}")

            try:
                await page.goto(url, wait_until="networkidle", timeout=60000)
            except Exception as exc:
                print(f"  ✗ ページ取得失敗: {exc}")
                break

            await page.wait_for_timeout(2000)
            companies = await page.query_selector_all("div.gyosha_results_container")
            if not companies:
                print("  ✓ 取得対象が見つからないため終了")
                break

            for idx, company_el in enumerate(companies, start=1):
                data = await extract_company_data(company_el, page_number, idx)
                detail_page = await context.new_page()
                detail_data = await extract_company_detail(detail_page, data.get("会社URL", ""))
                await detail_page.close()
                data.update(detail_data)
                if data["不用品回収業者名"]:
                    results.append(data)
                    print(f"  ✓ {idx}. {data['不用品回収業者名']}")
                else:
                    print(f"  ⚠ {idx}. 業者名を取得できませんでした")

            page_number += 1
            await asyncio.sleep(1.5)

        await browser.close()

    return results


async def main() -> None:
    print("=" * 60)
    print("不用品回収センター 比較ページ スクレイピング開始")
    print("=" * 60)

    data = await scrape_all_pages()
    df = pd.DataFrame(data)

    columns_order = [
        "ページ",
        "順位",
        "会社ID",
        "不用品回収業者名",
        "会社URL",
        "ロゴ画像URL",
        "ロゴ画像ALT",
        "総合評価（星）",
        "総合評価（テキスト）",
        "口コミ件数",
        "概要",
        "サービス内容",
        "対応エリア",
        "料金",
        "口コミタイトル",
        "口コミユーザー",
        "口コミ評価",
        "口コミ内訳",
        "口コミ本文",
        "口コミ画像URL",
        "詳細ページURL",
        "詳細ページロゴ画像URL",
        "詳細ページロゴ画像ALT",
        "詳細ページ総合評価（星）",
        "詳細ページ総合評価（テキスト）",
        "詳細ページ口コミ件数",
        "詳細ページ回収料金",
        "詳細ページ回収速度",
        "詳細ページ対応の良さ",
        "おすすめポイント",
        "特徴",
        "公式ホームページURL",
    ]

    for column in columns_order:
        if column not in df.columns:
            df[column] = ""

    df = df[columns_order]

    df.to_excel(OUTPUT_FILE, index=False, engine="openpyxl")

    print("=" * 60)
    print("✓ スクレイピング完了")
    print(f"✓ 総件数: {len(df)}件")
    print(f"✓ 出力ファイル: {OUTPUT_FILE}")
    print("=" * 60)


if __name__ == "__main__":
    asyncio.run(main())
