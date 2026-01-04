#!/usr/bin/env python3
"""
不用品回収コラム自動投稿スクリプト
毎日10時に実行され、SEO最適化された記事を生成してWordPressに投稿
"""

import os
import json
import random
from datetime import datetime
from pathlib import Path
import requests
from openai import OpenAI

# 設定
SCRIPT_DIR = Path(__file__).parent
KEYWORDS_FILE = SCRIPT_DIR / "keywords.json"
LOG_FILE = SCRIPT_DIR / "post_log.txt"

# WordPress設定（環境変数から取得）
WP_URL = os.environ.get("WP_URL", "https://umarekawari.online")
WP_USERNAME = os.environ.get("WP_USERNAME")
WP_APP_PASSWORD = os.environ.get("WP_APP_PASSWORD")

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
    
    log_message("AI記事生成を開始...")
    
    try:
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": "あなたは不用品回収業界の専門ライターです。"},
                {"role": "user", "content": prompt}
            ],
            temperature=0.7,
            max_tokens=4000
        )
        
        article = response.choices[0].message.content
        log_message("AI記事生成完了")
        
        return article
        
    except Exception as e:
        log_message(f"AI記事生成エラー: {str(e)}")
        raise

def extract_title_and_content(article):
    """記事からタイトルと本文を抽出"""
    lines = article.strip().split("\n")
    
    # 最初の見出しをタイトルとして抽出
    title = ""
    content_lines = []
    title_found = False
    
    for line in lines:
        if line.startswith("# ") and not title_found:
            title = line.replace("# ", "").strip()
            title_found = True
        elif title_found:
            content_lines.append(line)
    
    content = "\n".join(content_lines).strip()
    
    # タイトルが見つからない場合は最初の行を使用
    if not title and lines:
        title = lines[0].strip("#").strip()
        content = "\n".join(lines[1:]).strip()
    
    return title, content

def post_to_wordpress(title, content, keyword):
    """WordPressに記事を投稿"""
    if not WP_USERNAME or not WP_APP_PASSWORD:
        log_message("エラー: WordPress認証情報が設定されていません")
        return False
    
    # REST APIエンドポイント
    api_url = f"{WP_URL}/wp-json/wp/v2/posts"
    
    # 投稿データ
    post_data = {
        "title": title,
        "content": content,
        "status": "publish",  # "draft" にすると下書き保存
        "categories": [],  # カテゴリーIDを指定可能
        "tags": keyword.split(),  # キーワードをタグとして追加
        "meta": {
            "_yoast_wpseo_focuskw": keyword,  # Yoast SEO用（プラグインがあれば）
            "_yoast_wpseo_metadesc": f"{keyword}について詳しく解説します。"
        }
    }
    
    log_message("WordPressへの投稿を開始...")
    
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
            return True
        else:
            log_message(f"投稿失敗: ステータスコード={response.status_code}, レスポンス={response.text}")
            return False
            
    except Exception as e:
        log_message(f"WordPress投稿エラー: {str(e)}")
        return False

def main():
    """メイン処理"""
    log_message("=" * 60)
    log_message("自動投稿スクリプト開始")
    
    try:
        # 1. 次のキーワードを取得
        keyword = get_next_keyword()
        
        # 2. AI記事生成
        article = generate_article(keyword)
        
        # 3. タイトルと本文を抽出
        title, content = extract_title_and_content(article)
        
        log_message(f"生成された記事: タイトル='{title}', 本文長={len(content)}文字")
        
        # 4. WordPressに投稿
        success = post_to_wordpress(title, content, keyword)
        
        if success:
            log_message("✅ 自動投稿完了")
        else:
            log_message("❌ 自動投稿失敗")
            
    except Exception as e:
        log_message(f"❌ エラーが発生しました: {str(e)}")
        raise
    
    log_message("自動投稿スクリプト終了")
    log_message("=" * 60)

if __name__ == "__main__":
    main()
