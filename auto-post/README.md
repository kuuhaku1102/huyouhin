# コラム自動投稿機能 - 完全ガイド

## 概要

このシステムは、毎日10時（日本時間）に自動的にSEO最適化されたブログ記事を生成し、WordPressに投稿します。キーワードが重複しないように管理し、全キーワードを網羅的にカバーします。

## 主要機能

### 1. キーワード管理システム

- **56種類のキーワード**を自動ローテーション
- **重複防止**: 使用済みキーワードを追跡
- **自動リセット**: 全キーワード使用後に自動的に新サイクル開始
- **進捗管理**: 使用状況をJSONファイルで永続化

### 2. 自動投稿スクリプト

- **毎日10時に自動実行**（GitHub Actions）
- **AIによる記事生成**: GPT-4を使用してSEO最適化されたコンテンツを生成
- **WordPress自動投稿**: REST APIを使用して投稿
- **エラーハンドリング**: 失敗時のログ記録

### 3. スケジュール管理

- **cron**による定期実行
- **ログ記録**: すべての実行履歴を保存
- **柔軟な設定**: 時刻やステータスをカスタマイズ可能

## ファイル構成

```
auto-post/
├── generate_and_post.py   # メインスクリプト
├── keywords.json           # キーワードリスト＆進捗管理
├── post_log.txt           # 実行ログ
└── README.md              # このファイル

.github/workflows/
└── auto-post-column.yml   # GitHub Actionsワークフロー
```

## セットアップ

### 1. GitHubシークレットの設定

GitHubリポジトリの「Settings」→「Secrets and variables」→「Actions」で以下のシークレットを追加:

| シークレット名 | 説明 | 例 |
|--------------|------|-----|
| `OPENAI_API_KEY` | OpenAI APIキー | `sk-...` |
| `WP_URL` | WordPressサイトURL | `https://umarekawari.online` |
| `WP_USERNAME` | WordPress管理者ユーザー名 | `admin` |
| `WP_APP_PASSWORD` | WordPressアプリケーションパスワード | `xxxx xxxx xxxx xxxx` |

### 2. WordPressアプリケーションパスワードの作成

1. WordPress管理画面にログイン
2. 「ユーザー」→「プロフィール」
3. 下にスクロールして「アプリケーションパスワード」セクション
4. 新しいアプリケーション名を入力（例: "GitHub Actions"）
5. 「新しいアプリケーションパスワードを追加」をクリック
6. 生成されたパスワードをコピーしてGitHubシークレットに保存

### 3. WordPress REST APIの有効化

WordPressのREST APIはデフォルトで有効ですが、以下を確認:

- パーマリンク設定が「投稿名」または「カスタム構造」になっていること
- `.htaccess`が正しく設定されていること
- セキュリティプラグインがREST APIをブロックしていないこと

## 使い方

### 自動実行（推奨）

GitHub Actionsが毎日10時（日本時間）に自動実行します。何もする必要はありません。

### 手動実行

1. **GitHub Actionsから実行**:
   - GitHubリポジトリの「Actions」タブ
   - 「Auto Post Column Daily」ワークフローを選択
   - 「Run workflow」ボタンをクリック

2. **ローカルで実行**（テスト用）:
   ```bash
   cd auto-post
   
   # 環境変数を設定
   export OPENAI_API_KEY="your-api-key"
   export WP_URL="https://umarekawari.online"
   export WP_USERNAME="your-username"
   export WP_APP_PASSWORD="your-app-password"
   
   # スクリプトを実行
   python generate_and_post.py
   ```

## キーワードリスト

現在56種類のキーワードが登録されています:

- 不用品回収関連（料金、安い、即日、口コミ、比較など）
- 粗大ゴミ関連（回収、処分方法、料金など）
- 遺品整理関連（業者、料金、相場など）
- ゴミ屋敷関連（清掃、片付け、業者など）
- 生前整理関連（方法、業者、費用）
- 引越し関連（不用品処分、ゴミ処分など）
- 家具・家電処分（ベッド、ソファ、冷蔵庫など）
- その他（断捨離、片付け業者など）

## 進捗確認

`keywords.json`ファイルで進捗を確認できます:

```json
{
  "keywords": [...],
  "used_keywords": ["不用品回収 料金", "粗大ゴミ 回収", ...],
  "current_cycle": 1,
  "last_updated": "2025-01-05T10:00:00"
}
```

- `used_keywords`: 使用済みキーワードのリスト
- `current_cycle`: 現在のサイクル番号
- `last_updated`: 最終更新日時

## ログ確認

`post_log.txt`ファイルですべての実行履歴を確認できます:

```
[2025-01-05 10:00:00] ============================================================
[2025-01-05 10:00:00] 自動投稿スクリプト開始
[2025-01-05 10:00:05] 選択されたキーワード: 不用品回収 料金 (進捗: 1/56)
[2025-01-05 10:00:10] AI記事生成を開始...
[2025-01-05 10:00:45] AI記事生成完了
[2025-01-05 10:00:46] 生成された記事: タイトル='不用品回収の料金相場を徹底解説', 本文長=2500文字
[2025-01-05 10:00:47] WordPressへの投稿を開始...
[2025-01-05 10:00:50] 投稿成功: ID=123, URL=https://umarekawari.online/column/...
[2025-01-05 10:00:50] ✅ 自動投稿完了
```

## カスタマイズ

### 実行時刻の変更

`.github/workflows/auto-post-column.yml`の`cron`設定を変更:

```yaml
schedule:
  # 毎日15時（日本時間）= 6時（UTC）に実行
  - cron: '0 6 * * *'
```

### 投稿ステータスの変更

`generate_and_post.py`の`post_to_wordpress`関数内:

```python
post_data = {
    "status": "draft",  # "publish"（公開）または "draft"（下書き）
    ...
}
```

### キーワードの追加

`keywords.json`の`keywords`配列に追加:

```json
{
  "keywords": [
    "不用品回収 料金",
    "新しいキーワード1",
    "新しいキーワード2"
  ],
  ...
}
```

## トラブルシューティング

### 投稿が失敗する

1. **WordPress認証情報を確認**:
   - GitHubシークレットが正しく設定されているか
   - アプリケーションパスワードが有効か

2. **REST APIが有効か確認**:
   ```bash
   curl https://umarekawari.online/wp-json/wp/v2/posts
   ```

3. **ログを確認**:
   - GitHub Actionsの実行ログ
   - `post_log.txt`ファイル

### AI記事生成が失敗する

1. **OpenAI APIキーを確認**:
   - GitHubシークレットが正しく設定されているか
   - APIキーが有効で残高があるか

2. **レート制限を確認**:
   - OpenAIのレート制限に達していないか

### キーワードが重複する

`keywords.json`の`used_keywords`配列を手動でリセット:

```json
{
  "used_keywords": [],
  "current_cycle": 2
}
```

## セキュリティ

- **APIキーの保護**: GitHubシークレットを使用し、コードに直接記載しない
- **アプリケーションパスワード**: WordPress管理者パスワードではなく、専用のアプリケーションパスワードを使用
- **権限の最小化**: WordPressユーザーに必要最小限の権限のみ付与

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。
