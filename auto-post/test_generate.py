#!/usr/bin/env python3
"""
テスト記事生成スクリプト（WordPress投稿なし）
"""

import os
import json
from datetime import datetime
from pathlib import Path
from openai import OpenAI

# 設定
SCRIPT_DIR = Path(__file__).parent
KEYWORDS_FILE = SCRIPT_DIR / "keywords.json"
OUTPUT_FILE = SCRIPT_DIR / "test_article.md"

# OpenAI設定
OPENAI_API_KEY = os.environ.get("OPENAI_API_KEY")

def generate_article(keyword):
    """AIを使用してSEO最適化された記事を生成"""
    client = OpenAI(api_key=OPENAI_API_KEY)
    
    prompt = f"""
あなたは不用品回収業界の専門家です。以下のキーワードに基づいて、SEO最適化されたブログ記事を作成してください。

キーワード: {keyword}

要件:
1. タイトル: キーワードを含む魅力的なタイトル（30-40文字）
2. 本文: 2000-3000文字の詳細な記事
3. 構成:
   - 導入（問題提起）
   - 本文（具体的な情報、メリット・デメリット、比較、料金相場など）
   - まとめ（行動喚起）
4. SEO対策:
   - キーワードを自然に含める（タイトル、見出し、本文）
   - 見出しを適切に使用（H2、H3）
   - 読みやすい段落構成
5. 口調: 親しみやすく、専門的
6. 形式: Markdown形式で出力

記事を生成してください。
"""
    
    print(f"キーワード「{keyword}」で記事を生成中...")
    
    response = client.chat.completions.create(
        model="gpt-4.1-mini",
        messages=[
            {"role": "system", "content": "あなたは不用品回収業界の専門ライターです。"},
            {"role": "user", "content": prompt}
        ],
        temperature=0.7,
        max_tokens=4000
    )
    
    article = response.choices[0].message.content
    print("記事生成完了！")
    
    return article

def main():
    keyword = "不用品回収 料金相場"
    
    print("=" * 60)
    print("テスト記事生成開始")
    print("=" * 60)
    
    # 記事生成
    article = generate_article(keyword)
    
    # ファイルに保存
    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        f.write(article)
    
    print(f"\n記事を保存しました: {OUTPUT_FILE}")
    print(f"文字数: {len(article)}文字")
    print("\n--- 記事プレビュー ---")
    print(article[:500] + "...\n")
    print("=" * 60)

if __name__ == "__main__":
    main()
