#!/usr/bin/env python3
"""
不用品回収コラム自動投稿スクリプト（SEO強化版）
毎日10時に実行され、SEO最適化された記事を生成してWordPressに投稿
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

def log_message(message):
    """ログメッセージを記録"""
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_entry = f"[{timestamp}] {message}\n"
    print(log_entry.strip())
    
    with open(LOG_FILE, "a", encoding="utf-8") as f:
        f.write(log_entry)

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

def generate_article(keyword):
    """AIを使用してSEO最適化された記事を生成"""
    client = OpenAI(api_key=OPENAI_API_KEY)
    
    prompt = f"""
あなたは不用品回収業界のSEO専門ライターです。以下のキーワードに基づいて、検索エンジンで上位表示される高品質な記事を作成してください。

【メインキーワード】
{keyword}

【記事作成要件】

## 1. タイトル（必須）
- メインキーワードを必ず含める
- 30-35文字以内
- 数字や具体性を含める（例：「5つの方法」「2024年最新」）
- ユーザーの検索意図に応える
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
以下の要素を含めてください：

**H2見出し（3-5個）**
- キーワードを自然に含める
- 具体的で分かりやすい
- 検索意図に応える内容

**H3見出し（各H2に2-3個）**
- より詳細な情報
- 箇条書きや表を活用

**含めるべき情報：**
- 料金相場（具体的な数字）
- メリット・デメリット
- 選び方のポイント（3-5個）
- よくある失敗例と対策
- 業者選びのチェックリスト
- 実際の事例や体験談風の内容
- 地域による違い（該当する場合）
- 法律や規制の情報（該当する場合）

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
- 関連キーワードも適度に含める

### 内部リンク提案
- 関連する可能性のある記事テーマを3-5個提案
- 例：「[不用品回収の料金相場](#)」「[信頼できる業者の選び方](#)」

### 構造化データ対応
- FAQ形式の質問と回答を3-5個含める
- 「よくある質問」セクションとして独立させる

### 読みやすさ
- 1段落3-4文（100-150文字）
- 適度に改行
- 箇条書きや表を活用
- 専門用語には簡単な説明を添える

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

## 6. 注意事項
- 自然で読みやすい文章
- 過度なキーワード詰め込みは避ける
- 信頼性のある情報
- ユーザーファーストの視点
- 独自性のある内容
- 最新の情報（2024-2026年）

それでは、上記の要件に従って記事を作成してください。
"""
    
    log_message("AI記事生成を開始...")
    
    try:
        response = client.chat.completions.create(
            model="gpt-4.1-mini",
            messages=[
                {"role": "system", "content": "あなたは不用品回収業界のSEO専門ライターです。検索エンジンで上位表示される高品質な記事を作成することが得意です。"},
                {"role": "user", "content": prompt}
            ],
            temperature=0.7,
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
    # markdown拡張機能を有効化
    html = markdown.markdown(
        md_text,
        extensions=[
            'extra',  # テーブル、脚注、略語などをサポート
            'nl2br',  # 改行を<br>に変換
            'sane_lists',  # リストの処理を改善
            'toc',  # 目次生成
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
    content += "## 無料で見積もりを取る\n\n"
    content += "当サイトでは、複数の優良業者から一括見積もりを取ることができます。簡単30秒で最適な業者が見つかります。\n\n"
    content += "[無料見積もりはこちら](/quote)\n"
    
    # MarkdownをHTMLに変換
    html_content = markdown_to_html(content)
    
    return html_content

def post_to_wordpress(article_json, keyword):
    """WordPressに記事を投稿"""
    if not WP_USERNAME or not WP_APP_PASSWORD:
        log_message("エラー: WordPress認証情報が設定されていません")
        return False
    
    title = article_json.get("title", f"{keyword}について")
    meta_description = article_json.get("meta_description", "")
    content = format_article_content(article_json)
    
    # REST APIエンドポイント
    api_url = f"{WP_URL}/wp-json/wp/v2/posts"
    
    # 投稿データ
    post_data = {
        "title": title,
        "content": content,
        "excerpt": meta_description,  # メタディスクリプションを抜粋として設定
        "status": "publish",  # "draft" にすると下書き保存
        "categories": [],  # カテゴリーIDを指定可能
    }
    
    log_message("WordPressへの投稿を開始...")
    log_message(f"タイトル: {title}")
    log_message(f"メタディスクリプション: {meta_description}")
    log_message(f"本文長: {len(content)}文字")
    
    try:
        response = requests.post(
            api_url,
            json=post_data,
            auth=(WP_USERNAME, WP_APP_PASSWORD),
            timeout=30
        )
        
        if response.status_code == 201:
            post_id = response.json().get("id")
            post_url = response.json().get("link")
            log_message(f"投稿成功: ID={post_id}, URL={post_url}")
            
            # 記事データをログに保存
            log_article_details(keyword, title, meta_description, len(content), post_url)
            
            return True
        else:
            log_message(f"投稿失敗: ステータスコード={response.status_code}, レスポンス={response.text}")
            return False
            
    except Exception as e:
        log_message(f"WordPress投稿エラー: {str(e)}")
        return False

def log_article_details(keyword, title, meta_description, content_length, url):
    """記事の詳細情報をログに記録"""
    log_message("=" * 40)
    log_message(f"キーワード: {keyword}")
    log_message(f"タイトル: {title}")
    log_message(f"メタディスクリプション: {meta_description}")
    log_message(f"本文長: {content_length}文字")
    log_message(f"URL: {url}")
    log_message("=" * 40)

def main():
    """メイン処理"""
    log_message("=" * 60)
    log_message("自動投稿スクリプト開始（SEO強化版）")
    
    try:
        # 1. 次のキーワードを取得
        keyword = get_next_keyword()
        
        # 2. AI記事生成
        article_json = generate_article(keyword)
        
        # 3. WordPressに投稿
        success = post_to_wordpress(article_json, keyword)
        
        if success:
            log_message("✅ 自動投稿完了")
        else:
            log_message("❌ 自動投稿失敗")
            
    except Exception as e:
        log_message(f"❌ エラーが発生しました: {str(e)}")
        import traceback
        log_message(traceback.format_exc())
        raise
    
    log_message("自動投稿スクリプト終了")
    log_message("=" * 60)

if __name__ == "__main__":
    main()
