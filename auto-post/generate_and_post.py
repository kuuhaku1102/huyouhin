#!/usr/bin/env python3
"""
不用品回収コラム自動投稿スクリプト（2026年SEO完全準拠版）
- カテゴリ自動割り当て
- タイトルパターン多様化
- 役割分担の明確化
- 内部リンク自動追加
- 構造化データ対応
"""

import os
import json
import random
from datetime import datetime
from pathlib import Path
import requests
from openai import OpenAI
import markdown

# 設定
SCRIPT_DIR = Path(__file__).parent
KEYWORDS_FILE = SCRIPT_DIR / "keywords.json"
LOG_FILE = SCRIPT_DIR / "post_log.txt"

# WordPress設定（環境変数から取得）
WP_URL = os.environ.get("WP_URL", "https://umarekawari.online")
WP_USERNAME = os.environ.get("WP_USER")
WP_APP_PASSWORD = os.environ.get("WP_PASS")

# OpenAI設定
OPENAI_API_KEY = os.environ.get("OPENAI_API_KEY")

# カテゴリマッピング（キーワードからカテゴリを自動判定）
CATEGORY_MAPPING = {
    "basics": {
        "id": None,  # WordPressから取得
        "name": "不用品回収の基礎知識",
        "keywords": ["不用品回収 料金", "不用品回収 安い", "不用品回収 比較", "不用品回収 おすすめ", "不用品回収 口コミ", "不用品回収 選び方"],
        "title_patterns": [
            "{keyword}｜失敗しない業者選びの完全ガイド",
            "{keyword}を徹底解説｜初心者でも安心の選び方",
            "{keyword}の真実｜知らないと損する重要ポイント",
            "{keyword}｜プロが教える賢い選択術",
        ]
    },
    "item_guide": {
        "id": None,
        "name": "品目別処分ガイド",
        "keywords": ["エアコン 処分", "冷蔵庫 処分", "洗濯機 処分", "テレビ 処分", "ソファ 処分", "ベッド 処分", "衣類 処分", "本 処分", "家具 処分", "家電 処分", "粗大ゴミ 処分"],
        "title_patterns": [
            "{keyword}の完全ガイド｜費用・手順・注意点を徹底解説",
            "{keyword}｜最適な方法と料金相場を比較",
            "{keyword}で失敗しないための実践マニュアル",
            "{keyword}｜プロが教える正しい処分手順",
        ]
    },
    "situation_guide": {
        "id": None,
        "name": "状況別の処分方法",
        "keywords": ["不用品回収 無料", "不用品回収 即日", "引越し 不用品", "遺品整理", "ゴミ屋敷 料金", "ゴミ屋敷 片付け", "大量 処分"],
        "title_patterns": [
            "{keyword}｜状況別の最適な解決策を紹介",
            "{keyword}で困ったときの対処法完全版",
            "{keyword}｜ケース別に見る賢い選択",
            "{keyword}｜シーン別の実践的アドバイス",
        ]
    }
}

# タイトルパターンのバリエーション（「5つの方法」「2024年最新」を避ける）
TITLE_VARIATIONS = [
    "{keyword}｜{action}",
    "{keyword}の{aspect}｜{detail}",
    "{keyword}で{benefit}",
    "{keyword}｜{target}のための{guide_type}",
]

def log_message(message):
    """ログメッセージを記録"""
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_entry = f"[{timestamp}] {message}\n"
    print(log_entry.strip())
    
    with open(LOG_FILE, "a", encoding="utf-8") as f:
        f.write(log_entry)

def get_wp_category_ids():
    """WordPressからカテゴリIDを取得"""
    try:
        url = f"{WP_URL}/wp-json/wp/v2/column_category"
        response = requests.get(url)
        response.raise_for_status()
        
        categories = response.json()
        category_map = {}
        
        for cat in categories:
            name = cat["name"]
            cat_id = cat["id"]
            
            # カテゴリ名からマッピング
            if "基礎知識" in name:
                CATEGORY_MAPPING["basics"]["id"] = cat_id
            elif "品目別" in name:
                CATEGORY_MAPPING["item_guide"]["id"] = cat_id
            elif "状況別" in name:
                CATEGORY_MAPPING["situation_guide"]["id"] = cat_id
        
        log_message(f"カテゴリID取得完了: {CATEGORY_MAPPING}")
        
    except Exception as e:
        log_message(f"カテゴリID取得エラー: {str(e)}")

def determine_category(keyword):
    """キーワードから適切なカテゴリを判定"""
    for cat_key, cat_data in CATEGORY_MAPPING.items():
        for cat_keyword in cat_data["keywords"]:
            if cat_keyword in keyword:
                return cat_key, cat_data
    
    # デフォルトは基礎知識
    return "basics", CATEGORY_MAPPING["basics"]

def generate_diverse_title(keyword, category_data):
    """多様なタイトルパターンを生成"""
    patterns = category_data.get("title_patterns", [])
    if patterns:
        pattern = random.choice(patterns)
        return pattern.format(keyword=keyword)
    
    # フォールバック
    return f"{keyword}｜完全ガイド"

def load_keywords():
    """キーワードデータを読み込み"""
    try:
        with open(KEYWORDS_FILE, "r", encoding="utf-8") as f:
            return json.load(f)
    except FileNotFoundError:
        log_message("エラー: keywords.json が見つかりません")
        raise
    except json.JSONDecodeError:
        log_message("エラー: keywords.json の形式が不正です")
        raise

def save_keywords(data):
    """キーワードデータを保存"""
    data["last_updated"] = datetime.now().isoformat()
    with open(KEYWORDS_FILE, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

def get_next_keyword():
    """次のキーワードを取得（重複なし）"""
    data = load_keywords()
    
    available_keywords = [k for k in data["keywords"] if k not in data["used_keywords"]]
    
    # すべて使用済みの場合はリセット
    if not available_keywords:
        log_message(f"サイクル {data['current_cycle']} 完了。新しいサイクルを開始します。")
        data["used_keywords"] = []
        data["current_cycle"] += 1
        available_keywords = data["keywords"]
    
    # ランダムに1つ選択
    keyword = random.choice(available_keywords)
    data["used_keywords"].append(keyword)
    
    save_keywords(data)
    
    log_message(f"選択されたキーワード: {keyword} (進捗: {len(data['used_keywords'])}/{len(data['keywords'])})")
    
    return keyword

def generate_article(keyword, category_key, category_data):
    """AIを使用して2026年SEO準拠の記事を生成"""
    client = OpenAI(api_key=OPENAI_API_KEY)
    
    # カテゴリに応じた役割を定義
    role_description = {
        "basics": "初心者向けの基本情報を分かりやすく解説する",
        "item_guide": "特定の品目の処分方法を詳しく解説する",
        "situation_guide": "特定の状況における最適な解決策を提案する"
    }
    
    role = role_description.get(category_key, "不用品回収に関する情報を提供する")
    
    prompt = f"""
あなたは不用品回収業界のSEO専門ライターです。2026年のGoogleスパムポリシーを理解し、高品質な記事を作成します。

【重要な制約】
❌ 「5つの方法」「◯つの方法」というタイトルパターンは使用禁止
❌ 「2024年最新」「2025年最新」という表現は使用禁止
❌ 他の記事と同じ構成テンプレートは使用禁止
✅ 独自性のある構成で、このキーワード特有の情報を提供する

【メインキーワード】
{keyword}

【記事の役割】
カテゴリ: {category_data['name']}
役割: {role}

【記事作成要件】

## 1. タイトル（必須）
- メインキーワードを必ず含める
- 30-35文字以内
- 「5つの方法」「2024年最新」は使用禁止
- このキーワード特有の価値を表現
- クリックしたくなる魅力的な表現

## 2. メタディスクリプション（必須）
- 120-160文字
- キーワードを自然に含める
- 記事の要約と読むメリットを明確に
- 行動喚起を含める

## 3. 記事構成（2500-3500文字）

### 導入部（200-300文字）
- 読者の悩みや疑問を明確化
- この記事で得られる情報を提示
- 共感を呼ぶ表現

### 本文（2000-2800文字）
**このキーワード特有の情報を含めること：**

- カテゴリに応じた具体的な情報
  - 基礎知識: 業者選びの基本、初心者の失敗、見積もりの取り方
  - 品目別: 品目特有の注意点、リサイクル法、買取可能性
  - 状況別: 状況特有の問題点、最適な解決策、費用を抑える方法

**H2見出し（3-5個）**
- キーワードを自然に含める
- 具体的で分かりやすい
- 他の記事と差別化された見出し

**H3見出し（各H2に2-3個）**
- より詳細な情報
- 箇条書きや表を活用

### まとめ（200-300文字）
- 記事の要点を3-5点で整理
- 次のアクションを提案
- 当サイトの見積もりサービスへの誘導

## 4. SEO最適化要件

### キーワード配置
- タイトルに1回
- メタディスクリプションに1回
- 最初の100文字以内に1回
- H2見出しに2-3回
- 本文全体で自然に5-8回（キーワード密度1-2%）

### 内部リンク提案
- 同じカテゴリ内の関連記事テーマを3個提案
- 例：「[◯◯の選び方](#)」

### 構造化データ対応
- FAQ形式の質問と回答を3-5個含める

## 5. 出力形式

以下のJSON形式で出力してください：

```json
{{
  "title": "記事タイトル",
  "meta_description": "メタディスクリプション",
  "content": "Markdown形式の本文",
  "internal_links": [
    {{"text": "リンクテキスト", "theme": "記事テーマ"}},
    ...
  ],
  "faq": [
    {{"question": "質問", "answer": "回答"}},
    ...
  ]
}}
```

## 6. 2026年SEO準拠の注意事項
- ✅ 独自性のある内容（他の記事と8割以上重複しない）
- ✅ 検索意図を満たす具体的な情報
- ✅ ユーザーファーストの視点
- ✅ 信頼性のある情報
- ❌ 量産型のテンプレート構成
- ❌ 過度なキーワード詰め込み
- ❌ 薄い内容の記事

それでは、上記の要件に従って記事を作成してください。
"""
    
    log_message("AI記事生成を開始...")
    
    try:
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": "あなたは2026年のGoogleスパムポリシーを理解した不用品回収業界のSEO専門ライターです。量産型コンテンツを避け、独自性のある高品質な記事を作成します。"},
                {"role": "user", "content": prompt}
            ],
            temperature=0.8,  # 多様性を高める
            max_tokens=6000,
            response_format={"type": "json_object"}
        )
        
        article_json = json.loads(response.choices[0].message.content)
        log_message("AI記事生成完了")
        
        return article_json
        
    except Exception as e:
        log_message(f"AI記事生成エラー: {str(e)}")
        raise

def markdown_to_html(md_text):
    """MarkdownをHTMLに変換"""
    html = markdown.markdown(
        md_text,
        extensions=[
            'extra',
            'nl2br',
            'sane_lists',
            'toc',
        ]
    )
    return html

def format_article_content(article_json):
    """記事データを整形してHTMLに変換"""
    content = article_json.get("content", "")
    
    # FAQセクションを追加
    faq = article_json.get("faq", [])
    if faq:
        content += "\n\n## よくある質問\n\n"
        for item in faq:
            content += f"### {item['question']}\n\n"
            content += f"{item['answer']}\n\n"
    
    # 内部リンクセクションを追加
    internal_links = article_json.get("internal_links", [])
    if internal_links:
        content += "\n\n## 関連記事\n\n"
        for link in internal_links:
            content += f"- [{link['text']}](#)\n"
    
    # CTAを追加
    content += "\n\n---\n\n"
    content += "**無料で複数業者から見積もりを取る**\n\n"
    content += "当サイトでは、お住まいの地域に対応した優良な不用品回収業者を無料でご紹介しています。\n"
    content += "複数の業者から見積もりを取ることで、最適な業者を見つけることができます。\n\n"
    content += "[無料で見積もりを依頼する](/quote/)\n"
    
    # MarkdownをHTMLに変換
    html_content = markdown_to_html(content)
    
    return html_content

def post_to_wordpress(title, content, meta_description, category_id):
    """WordPressに記事を投稿"""
    url = f"{WP_URL}/wp-json/wp/v2/columns"
    
    data = {
        "title": title,
        "content": content,
        "status": "publish",
        "excerpt": meta_description,
        "column_category": [category_id] if category_id else [],  # カテゴリを割り当て
    }
    
    try:
        response = requests.post(
            url,
            json=data,
            auth=(WP_USERNAME, WP_APP_PASSWORD),
            headers={"Content-Type": "application/json"}
        )
        response.raise_for_status()
        
        post_data = response.json()
        post_id = post_data.get("id")
        post_url = post_data.get("link")
        
        log_message(f"投稿成功: ID={post_id}, URL={post_url}")
        
        return post_id, post_url
        
    except requests.exceptions.HTTPError as e:
        log_message(f"WordPress投稿エラー: {e.response.status_code} - {e.response.text}")
        raise
    except Exception as e:
        log_message(f"WordPress投稿エラー: {str(e)}")
        raise

def main():
    """メイン処理"""
    try:
        log_message("=== 自動投稿開始 ===")
        
        # 環境変数チェック
        if not all([WP_USERNAME, WP_APP_PASSWORD, OPENAI_API_KEY]):
            log_message("エラー: 環境変数が設定されていません")
            log_message(f"WP_USER: {'設定済み' if WP_USERNAME else '未設定'}")
            log_message(f"WP_PASS: {'設定済み' if WP_APP_PASSWORD else '未設定'}")
            log_message(f"OPENAI_API_KEY: {'設定済み' if OPENAI_API_KEY else '未設定'}")
            return
        
        # WordPressからカテゴリIDを取得
        get_wp_category_ids()
        
        # 次のキーワードを取得
        keyword = get_next_keyword()
        
        # カテゴリを判定
        category_key, category_data = determine_category(keyword)
        category_id = category_data.get("id")
        
        log_message(f"カテゴリ: {category_data['name']} (ID: {category_id})")
        
        # 記事を生成
        article = generate_article(keyword, category_key, category_data)
        
        # タイトルを取得（AIが生成したものを使用）
        title = article.get("title", generate_diverse_title(keyword, category_data))
        meta_description = article.get("meta_description", "")
        
        log_message(f"生成されたタイトル: {title}")
        
        # 記事を整形
        content = format_article_content(article)
        
        # WordPressに投稿
        post_id, post_url = post_to_wordpress(title, content, meta_description, category_id)
        
        log_message(f"✅ 自動投稿完了")
        log_message(f"キーワード: {keyword}")
        log_message(f"カテゴリ: {category_data['name']}")
        log_message(f"タイトル: {title}")
        log_message(f"投稿ID: {post_id}")
        log_message(f"URL: {post_url}")
        
    except Exception as e:
        log_message(f"❌ エラーが発生しました: {str(e)}")
        raise

if __name__ == "__main__":
    main()
