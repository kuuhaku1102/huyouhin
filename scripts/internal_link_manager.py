"""
内部リンク自動管理システム
投稿済み記事データベースを管理し、関連記事へのリンクを自動挿入します。
"""

import json
import re
from pathlib import Path
from typing import List, Dict, Optional
from datetime import datetime

class InternalLinkManager:
    def __init__(self, db_path: str = "data/internal_links_db.json"):
        self.db_path = Path(db_path)
        self.db = self._load_db()
    
    def _load_db(self) -> Dict:
        """データベースファイルを読み込み"""
        if self.db_path.exists():
            with open(self.db_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        return {"articles": []}
    
    def _save_db(self):
        """データベースファイルを保存"""
        self.db_path.parent.mkdir(parents=True, exist_ok=True)
        with open(self.db_path, 'w', encoding='utf-8') as f:
            json.dump(self.db, f, ensure_ascii=False, indent=2)
    
    def add_article(self, title: str, url: str, category: str, keywords: List[str]):
        """新しい記事をデータベースに追加"""
        article_id = f"article_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        
        article = {
            "id": article_id,
            "title": title,
            "url": url,
            "category": category,
            "keywords": keywords,
            "published_at": datetime.now().isoformat()
        }
        
        self.db["articles"].append(article)
        self._save_db()
        print(f"✅ 記事をデータベースに追加: {title}")
    
    def find_related_articles(self, category: str, keywords: List[str], max_results: int = 5) -> List[Dict]:
        """関連記事を検索"""
        scored_articles = []
        
        for article in self.db["articles"]:
            score = 0
            
            # カテゴリー一致: +10点
            if article["category"] == category:
                score += 10
            
            # キーワード一致: +5点/キーワード
            for keyword in keywords:
                if keyword in article["keywords"]:
                    score += 5
            
            if score > 0:
                scored_articles.append({
                    **article,
                    "score": score
                })
        
        # スコアでソート
        scored_articles.sort(key=lambda x: x["score"], reverse=True)
        
        return scored_articles[:max_results]
    
    def insert_internal_links(self, content: str, category: str, keywords: List[str], max_links: int = 5) -> str:
        """記事本文に内部リンクを自動挿入"""
        related_articles = self.find_related_articles(category, keywords, max_results=max_links)
        
        if not related_articles:
            print("⚠️  関連記事が見つかりませんでした")
            return content
        
        print(f"🔗 {len(related_articles)}件の関連記事を検出")
        
        inserted_count = 0
        modified_content = content
        
        for article in related_articles:
            if inserted_count >= max_links:
                break
            
            # 記事のキーワードを検索
            for keyword in article["keywords"]:
                if inserted_count >= max_links:
                    break
                
                # 単語境界でマッチング（既存のリンク内を除外）
                pattern = re.compile(r'\b(' + re.escape(keyword) + r')\b', re.IGNORECASE)
                
                # 最初の出現箇所を検索
                match = pattern.search(modified_content)
                
                if match:
                    start_pos = match.start()
                    
                    # リンク内かどうかをチェック
                    before_text = modified_content[:start_pos]
                    last_a_open = before_text.rfind('<a ')
                    last_a_close = before_text.rfind('</a>')
                    
                    # <a>の後に</a>がない場合はリンク内なのでスキップ
                    if last_a_open > last_a_close:
                        continue
                    
                    # リンクを挿入
                    link_html = f'<a href="{article["url"]}" target="_blank" rel="noopener">{match.group(1)}</a>'
                    modified_content = pattern.sub(link_html, modified_content, count=1)
                    
                    inserted_count += 1
                    print(f"  ✓ リンク挿入: {keyword} → {article['title']}")
        
        print(f"✅ 合計 {inserted_count}個の内部リンクを挿入しました")
        return modified_content
    
    def get_statistics(self) -> Dict:
        """データベース統計情報を取得"""
        total_articles = len(self.db["articles"])
        categories = {}
        
        for article in self.db["articles"]:
            cat = article["category"]
            categories[cat] = categories.get(cat, 0) + 1
        
        return {
            "total_articles": total_articles,
            "categories": categories
        }


if __name__ == "__main__":
    # テスト実行
    manager = InternalLinkManager()
    
    # 統計情報を表示
    stats = manager.get_statistics()
    print(f"📊 データベース統計:")
    print(f"  総記事数: {stats['total_articles']}")
    print(f"  カテゴリー別: {stats['categories']}")
