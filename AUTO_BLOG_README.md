# 自動ブログ投稿システム v3.0

## 概要

6ステップ品質管理プロセスで高品質なSEO記事を自動生成し、WordPressに投稿するシステムです。

## 主要機能

### 1. 6ステップ記事生成プロセス

1. **検索意図の定義** - ユーザーの悩みと検索意図を分析
2. **見出し構造の設計** - SEOに最適化された見出し構成
3. **セクション単位での本文生成** - 高品質な本文を段階的に生成
4. **全文の統合** - イントロとまとめを追加して完成
5. **品質ゲート** - 自動審査で品質を保証（合格点: 50/100）
6. **SEO最適化パッケージ** - タイトル案、メタディスクリプション、FAQ生成

### 2. 内部リンク自動化

- 投稿済み記事データベースを管理
- 関連キーワードを自動検出
- 適切なアンカーテキストでリンク挿入

### 3. カテゴリーローテーション

- 3つのカテゴリーを順番に投稿
- 各カテゴリー内で記事役割をローテーション
- バランスの取れたコンテンツ構成

## ファイル構成

```
scripts/
├── auto_blog_v3.py              # メインオーケストレーター
├── generate_article_v3.py       # 記事生成エンジン
├── post_to_wordpress_v2.py      # WordPress投稿
└── internal_link_manager.py     # 内部リンク管理

data/
├── articles/                    # 生成された記事（JSON）
├── article_history.json         # カテゴリーローテーション履歴
└── internal_links_db.json       # 内部リンク用記事データベース

seo_category_design.json         # SEO戦略と記事テーマの設計図
```

## 環境変数

以下の環境変数が必要です（GitHub Secretsに設定）：

- `OPENAI_API_KEY`: Gemini APIキー
- `WP_SITE_URL`: WordPressサイトURL（例: https://umarekawari.online）
- `WP_USER`: WordPressユーザー名
- `WP_PASS`: WordPressアプリケーションパスワード

## 実行方法

### ローカルでテスト実行

```bash
# 環境変数を設定
export OPENAI_API_KEY="your-api-key"
export WP_SITE_URL="https://umarekawari.online"
export WP_USER="your-username"
export WP_PASS="your-app-password"

# スクリプトを実行
cd scripts
python auto_blog_v3.py
```

### GitHub Actionsで自動実行

- 毎週火・木・土の10時（JST）に自動実行
- 手動実行も可能（Actions タブから "Run workflow"）

## カスタマイズ

### カテゴリーと記事役割の追加

`seo_category_design.json` を編集：

```json
{
  "categories": [
    {
      "slug": "new_category",
      "name": "新しいカテゴリー",
      "description": "説明",
      "article_roles": [
        {
          "role": "記事の役割",
          "purpose": "目的",
          "differentiation": "差別化ポイント",
          "priority": 1
        }
      ]
    }
  ]
}
```

### 品質基準の変更

`generate_article_v3.py` の `pass_score` を変更：

```python
self.pass_score = 50  # 合格点（0-100）
```

### 内部リンク数の調整

`post_to_wordpress_v2.py` の `max_links` を変更：

```python
content_with_links = self.link_manager.insert_internal_links(
    content_html,
    article_data["category"],
    article_data["seo"]["focus_keywords"],
    max_links=5  # 最大リンク数
)
```

## トラブルシューティング

### 品質ゲートで失敗する

- `pass_score` を下げる（推奨: 40-60）
- SEO設計図の `differentiation` を明確にする

### 内部リンクが挿入されない

- `internal_links_db.json` に記事が登録されているか確認
- 関連キーワードが適切に設定されているか確認

### WordPress投稿に失敗する

- アプリケーションパスワードが正しいか確認
- カテゴリーが存在するか確認（WordPressで事前作成が必要）

## ライセンス

MIT License
