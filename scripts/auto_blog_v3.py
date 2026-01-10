"""
自動ブログ投稿システム v3.0 - メインオーケストレーター
6ステップ品質管理プロセスで記事を生成し、WordPressに自動投稿します。
"""

import os
import json
import random
from pathlib import Path
from datetime import datetime
from generate_article_v3 import ArticleGenerator
from post_to_wordpress_v2 import WordPressPublisher

class AutoBlogSystem:
    def __init__(self):
        self.root_dir = Path(__file__).parent.parent
        self.data_dir = self.root_dir / "data"
        self.articles_dir = self.data_dir / "articles"
        
        # ディレクトリを作成
        self.data_dir.mkdir(exist_ok=True)
        self.articles_dir.mkdir(exist_ok=True)
        
        # SEO設計図を読み込み
        self.seo_design = self._load_seo_design()
        
        # 記事履歴を読み込み
        self.history = self._load_history()
        
        # コンポーネントを初期化
        self.generator = ArticleGenerator()
        self.publisher = WordPressPublisher()
    
    def _load_seo_design(self) -> dict:
        """SEO設計図を読み込み"""
        design_file = self.root_dir / "seo_category_design.json"
        
        if not design_file.exists():
            raise FileNotFoundError(f"❌ SEO設計図が見つかりません: {design_file}")
        
        with open(design_file, 'r', encoding='utf-8') as f:
            return json.load(f)
    
    def _load_history(self) -> dict:
        """記事履歴を読み込み"""
        history_file = self.data_dir / "article_history.json"
        
        if history_file.exists():
            with open(history_file, 'r', encoding='utf-8') as f:
                return json.load(f)
        
        # 初期化
        return {
            "last_category": None,
            "last_article_date": None,
            "category_progress": {}
        }
    
    def _save_history(self):
        """記事履歴を保存"""
        history_file = self.data_dir / "article_history.json"
        
        with open(history_file, 'w', encoding='utf-8') as f:
            json.dump(self.history, f, ensure_ascii=False, indent=2)
    
    def select_next_article(self) -> tuple:
        """次に生成する記事を選択"""
        print("\n🎯 次の記事テーマを選択中...")
        
        categories = self.seo_design["categories"]
        
        # カテゴリーをローテーション
        last_category = self.history.get("last_category")
        
        # 次のカテゴリーを選択
        if last_category:
            # 前回のカテゴリーの次を選択
            category_slugs = [cat["slug"] for cat in categories]
            try:
                last_index = category_slugs.index(last_category)
                next_index = (last_index + 1) % len(category_slugs)
            except ValueError:
                next_index = 0
        else:
            next_index = 0
        
        selected_category = categories[next_index]
        
        # カテゴリー内の記事役割を選択
        category_slug = selected_category["slug"]
        progress = self.history["category_progress"].get(category_slug, {
            "total_articles": 0,
            "last_priority": 0
        })
        
        # 優先度順にソート
        roles = sorted(
            selected_category["article_roles"],
            key=lambda x: x.get("priority", 99)
        )
        
        # 次の役割を選択
        last_priority = progress["last_priority"]
        next_role = None
        
        for role in roles:
            if role.get("priority", 99) > last_priority:
                next_role = role
                break
        
        # すべての役割を使い切った場合は最初に戻る
        if not next_role:
            next_role = roles[0]
        
        # カテゴリーのキーワードリストからランダムに選択
        if 'keywords' in selected_category and selected_category['keywords']:
            # 上位100件からランダムに選択（トラフィックの多いキーワードを優先）
            top_keywords = selected_category['keywords'][:100]
            selected_keyword = random.choice(top_keywords)
            # 記事役割にキーワードを追加
            next_role['selected_keyword'] = selected_keyword
            print(f"  ✓ カテゴリー: {selected_category['name']}")
            print(f"  ✓ 記事役割: {next_role['role']}")
            print(f"  ✓ ターゲットキーワード: {selected_keyword}")
        else:
            print(f"  ✓ カテゴリー: {selected_category['name']}")
            print(f"  ✓ 記事役割: {next_role['role']}")
        
        return selected_category, next_role
    
    def save_article_data(self, article_data: dict, post_data: dict):
        """記事データを保存"""
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"article_{timestamp}.json"
        filepath = self.articles_dir / filename
        
        data = {
            "generated_at": datetime.now().isoformat(),
            "article": article_data,
            "wordpress_post": {
                "id": post_data["id"],
                "url": post_data["link"],
                "title": post_data["title"]["rendered"]
            }
        }
        
        with open(filepath, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        
        print(f"  ✓ 記事データを保存: {filename}")
    
    def update_history(self, category_slug: str, role_priority: int):
        """記事履歴を更新"""
        self.history["last_category"] = category_slug
        self.history["last_article_date"] = datetime.now().strftime("%Y-%m-%d")
        
        if category_slug not in self.history["category_progress"]:
            self.history["category_progress"][category_slug] = {
                "total_articles": 0,
                "last_priority": 0
            }
        
        progress = self.history["category_progress"][category_slug]
        progress["total_articles"] += 1
        progress["last_priority"] = role_priority
        
        self._save_history()
        print(f"  ✓ 履歴を更新: {category_slug} ({progress['total_articles']}記事目)")
    
    def run(self):
        """メイン処理を実行"""
        print(f"\n{'='*60}")
        print(f"🚀 自動ブログ投稿システム v3.0 起動")
        print(f"{'='*60}")
        
        try:
            # Step 1: 次の記事テーマを選択
            category_data, role_data = self.select_next_article()
            
            # Step 2: 記事を生成
            article_data = self.generator.generate_article(category_data, role_data)
            
            # Step 3: WordPressに投稿
            post_data = self.publisher.publish_article(article_data)
            
            # Step 4: データを保存
            self.save_article_data(article_data, post_data)
            
            # Step 5: 履歴を更新
            self.update_history(
                category_data["slug"],
                role_data.get("priority", 99)
            )
            
            print(f"\n{'='*60}")
            print(f"✅ すべての処理が完了しました")
            print(f"{'='*60}")
            print(f"  記事URL: {post_data['link']}")
            print(f"  品質スコア: {article_data['quality_score']}/100")
            print(f"{'='*60}\n")
            
        except Exception as e:
            print(f"\n❌ エラーが発生しました: {e}")
            raise


if __name__ == "__main__":
    system = AutoBlogSystem()
    system.run()
