"""
WordPress投稿スクリプト v2.0
生成された記事をWordPressに投稿し、内部リンクを挿入します。
"""

import os
import base64
import requests
import markdown
from typing import Dict
from internal_link_manager import InternalLinkManager

class WordPressPublisher:
    def __init__(self):
        self.site_url = os.environ.get("WP_SITE_URL", "https://umarekawari.online")
        self.username = os.environ.get("WP_USER")
        self.app_password = os.environ.get("WP_PASS")
        
        if not self.username or not self.app_password:
            raise ValueError("❌ WordPress認証情報が設定されていません")
        
        # Basic認証ヘッダー
        credentials = f"{self.username}:{self.app_password}"
        token = base64.b64encode(credentials.encode()).decode()
        self.headers = {
            "Authorization": f"Basic {token}",
            "Content-Type": "application/json"
        }
        
        self.link_manager = InternalLinkManager()
    
    def get_category_id(self, category_slug: str) -> int:
        """カテゴリーIDを取得"""
        url = f"{self.site_url}/wp-json/wp/v2/categories"
        params = {"slug": category_slug}
        
        response = requests.get(url, params=params)
        response.raise_for_status()
        
        categories = response.json()
        if categories:
            return categories[0]["id"]
        
        raise ValueError(f"❌ カテゴリーが見つかりません: {category_slug}")
    
    def markdown_to_html(self, markdown_text: str) -> str:
        """MarkdownをHTMLに変換"""
        html = markdown.markdown(
            markdown_text,
            extensions=['extra', 'codehilite', 'toc']
        )
        return html
    
    def publish_article(self, article_data: Dict) -> Dict:
        """記事をWordPressに投稿"""
        print(f"\n{'='*60}")
        print(f"📤 WordPress投稿開始")
        print(f"{'='*60}\n")
        
        # カテゴリーIDを取得
        category_id = self.get_category_id(article_data["category"])
        print(f"✓ カテゴリーID取得: {category_id}")
        
        # MarkdownをHTMLに変換
        content_html = self.markdown_to_html(article_data["content"])
        print(f"✓ Markdown→HTML変換完了")
        
        # 内部リンクを挿入
        content_with_links = self.link_manager.insert_internal_links(
            content_html,
            article_data["category"],
            article_data["seo"]["focus_keywords"],
            max_links=5
        )
        
        # タイトルを選択（最初の候補を使用）
        title = article_data["seo"]["title_candidates"][0]
        
        # 投稿データを作成
        post_data = {
            "title": title,
            "content": content_with_links,
            "status": "publish",
            "categories": [category_id],
            "meta": {
                "description": article_data["seo"]["meta_description"]
            }
        }
        
        # WordPressに投稿
        url = f"{self.site_url}/wp-json/wp/v2/posts"
        response = requests.post(url, json=post_data, headers=self.headers)
        response.raise_for_status()
        
        post = response.json()
        
        print(f"\n✅ 投稿成功!")
        print(f"  タイトル: {post['title']['rendered']}")
        print(f"  URL: {post['link']}")
        print(f"  ID: {post['id']}")
        
        # 内部リンクDBに追加
        self.link_manager.add_article(
            title=title,
            url=post['link'],
            category=article_data["category"],
            keywords=article_data["seo"]["focus_keywords"]
        )
        
        return post


if __name__ == "__main__":
    # テスト実行
    publisher = WordPressPublisher()
    
    test_article = {
        "content": "# テスト記事\n\nこれはテスト記事です。",
        "category": "basics",
        "seo": {
            "title_candidates": ["テスト記事タイトル"],
            "meta_description": "これはテスト記事のメタディスクリプションです。",
            "focus_keywords": ["不用品回収", "テスト"]
        }
    }
    
    post = publisher.publish_article(test_article)
    print(f"\n投稿URL: {post['link']}")
