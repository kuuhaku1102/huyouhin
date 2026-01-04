#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
不用品回収センター スクレイピングスクリプト
全47都道府県の業者データを収集してExcel出力
"""

import asyncio
import pandas as pd
from playwright.async_api import async_playwright
import time
from typing import List, Dict

# 47都道府県のスラッグ定義
PREFECTURES = {
    "北海道": "hokkaido",
    "青森県": "aomori",
    "岩手県": "iwate",
    "宮城県": "miyagi",
    "秋田県": "akita",
    "山形県": "yamagata",
    "福島県": "fukushima",
    "茨城県": "ibaraki",
    "栃木県": "tochigi",
    "群馬県": "gunma",
    "埼玉県": "saitama",
    "千葉県": "chiba",
    "東京都": "tokyo",
    "神奈川県": "kanagawa",
    "新潟県": "niigata",
    "富山県": "toyama",
    "石川県": "ishikawa",
    "福井県": "fukui",
    "山梨県": "yamanashi",
    "長野県": "nagano",
    "岐阜県": "gifu",
    "静岡県": "shizuoka",
    "愛知県": "aichi",
    "三重県": "mie",
    "滋賀県": "shiga",
    "京都府": "kyoto",
    "大阪府": "osaka",
    "兵庫県": "hyogo",
    "奈良県": "nara",
    "和歌山県": "wakayama",
    "鳥取県": "tottori",
    "島根県": "shimane",
    "岡山県": "okayama",
    "広島県": "hiroshima",
    "山口県": "yamaguchi",
    "徳島県": "tokushima",
    "香川県": "kagawa",
    "愛媛県": "ehime",
    "高知県": "kochi",
    "福岡県": "fukuoka",
    "佐賀県": "saga",
    "長崎県": "nagasaki",
    "熊本県": "kumamoto",
    "大分県": "oita",
    "宮崎県": "miyazaki",
    "鹿児島県": "kagoshima",
    "沖縄県": "okinawa"
}

BASE_URL = "https://fuyouhincenter.jp"


async def scrape_prefecture(page, pref_name: str, pref_slug: str) -> List[Dict]:
    """
    指定都道府県の業者データをスクレイピング
    """
    url = f"{BASE_URL}/{pref_slug}/"
    print(f"スクレイピング中: {pref_name} ({url})")
    
    companies = []
    
    try:
        # ページにアクセス
        await page.goto(url, wait_until="networkidle", timeout=60000)
        
        # ページ読み込み待機
        await page.wait_for_timeout(3000)
        
        # 業者カードを取得（複数のセレクタを試行）
        selectors = [
            ".company-card",
            ".ranking-item",
            "article.company",
            ".company-list > div",
            "[class*='company']"
        ]
        
        company_elements = []
        for selector in selectors:
            company_elements = await page.query_selector_all(selector)
            if company_elements:
                print(f"  → {len(company_elements)}件の業者を発見（セレクタ: {selector}）")
                break
        
        if not company_elements:
            print(f"  ⚠ {pref_name}: 業者データが見つかりませんでした")
            return companies
        
        # 各業者の情報を抽出
        for idx, element in enumerate(company_elements, start=1):
            try:
                company_data = {
                    "都道府県": pref_name,
                    "順位": idx,
                    "不用品回収業者名": "",
                    "総合評価": "",
                    "概要": "",
                    "サービス内容": "",
                    "対応エリア": "",
                    "料金": ""
                }
                
                # 業者名を取得
                name_selectors = ["h3", "h4", ".company-name", "[class*='name']"]
                for sel in name_selectors:
                    name_el = await element.query_selector(sel)
                    if name_el:
                        company_data["不用品回収業者名"] = (await name_el.inner_text()).strip()
                        break
                
                # 総合評価を取得
                rating_selectors = [".rating", "[class*='rating']", "[class*='score']"]
                for sel in rating_selectors:
                    rating_el = await element.query_selector(sel)
                    if rating_el:
                        company_data["総合評価"] = (await rating_el.inner_text()).strip()
                        break
                
                # 概要を取得
                desc_selectors = ["p", ".description", "[class*='desc']"]
                for sel in desc_selectors:
                    desc_el = await element.query_selector(sel)
                    if desc_el:
                        desc_text = (await desc_el.inner_text()).strip()
                        # 改行・空行を削除
                        desc_text = " ".join(desc_text.split())
                        company_data["概要"] = desc_text[:200]  # 最大200文字
                        break
                
                # サービス内容を取得
                service_els = await element.query_selector_all("li, .service-item")
                if service_els:
                    services = []
                    for service_el in service_els:
                        service_text = (await service_el.inner_text()).strip()
                        if service_text and len(service_text) < 50:
                            services.append(service_text)
                    company_data["サービス内容"] = " / ".join(services[:10])
                
                # 対応エリアを取得
                area_el = await element.query_selector("[class*='area'], [class*='region']")
                if area_el:
                    area_text = (await area_el.inner_text()).strip()
                    # 区切り文字を統一
                    area_text = area_text.replace("｜", ",").replace("|", ",").replace("\n", ",")
                    company_data["対応エリア"] = area_text
                
                # 料金を取得
                price_els = await element.query_selector_all("[class*='price'], [class*='plan']")
                if price_els:
                    prices = []
                    for price_el in price_els:
                        price_text = (await price_el.inner_text()).strip()
                        if price_text and "円" in price_text:
                            prices.append(price_text)
                    company_data["料金"] = " / ".join(prices[:5])
                
                # 業者名が取得できた場合のみ追加
                if company_data["不用品回収業者名"]:
                    companies.append(company_data)
                    print(f"  ✓ {idx}. {company_data['不用品回収業者名']}")
                
            except Exception as e:
                print(f"  ✗ 業者{idx}のデータ取得エラー: {e}")
                continue
        
        print(f"  完了: {pref_name} - {len(companies)}件取得")
        
    except Exception as e:
        print(f"  ✗ {pref_name}のページ取得エラー: {e}")
    
    return companies


async def main():
    """
    メイン処理
    """
    print("=" * 60)
    print("不用品回収センター スクレイピング開始")
    print("=" * 60)
    
    all_companies = []
    
    async with async_playwright() as p:
        # ブラウザ起動
        browser = await p.chromium.launch(
            headless=True,
            args=['--no-sandbox', '--disable-setuid-sandbox']
        )
        
        # コンテキスト作成（User-Agent設定）
        context = await browser.new_context(
            user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
        )
        
        page = await context.new_page()
        
        # 全都道府県をスクレイピング
        for pref_name, pref_slug in PREFECTURES.items():
            companies = await scrape_prefecture(page, pref_name, pref_slug)
            all_companies.extend(companies)
            
            # サーバー負荷軽減のため待機
            await asyncio.sleep(2)
        
        await browser.close()
    
    # DataFrameに変換
    df = pd.DataFrame(all_companies)
    
    # カラム順序を指定
    columns_order = [
        "都道府県",
        "順位",
        "不用品回収業者名",
        "総合評価",
        "概要",
        "サービス内容",
        "対応エリア",
        "料金"
    ]
    
    # カラムが存在しない場合は空列を追加
    for col in columns_order:
        if col not in df.columns:
            df[col] = ""
    
    df = df[columns_order]
    
    # Excel出力
    output_file = "fuyouhincenter_all_prefectures.xlsx"
    df.to_excel(output_file, index=False, engine="openpyxl")
    
    print("=" * 60)
    print(f"✓ スクレイピング完了")
    print(f"✓ 総件数: {len(df)}件")
    print(f"✓ 出力ファイル: {output_file}")
    print("=" * 60)


if __name__ == "__main__":
    asyncio.run(main())
