# 不用品回収センター スクレイピングツール

## 概要

`https://fuyouhincenter.jp/` から全47都道府県の不用品回収業者データを自動収集し、Excel形式で出力するツールです。

## ファイル構成

```
scripts/
├── scrape_fuyouhincenter.py    # メインスクリプト
├── requirements.txt             # 依存パッケージ
├── README.md                    # このファイル
└── fuyouhincenter_all_prefectures.xlsx  # 出力ファイル（自動生成）
```

## 出力データ

### Excelファイル形式

- **ファイル名**: `fuyouhincenter_all_prefectures.xlsx`
- **形式**: Excel (.xlsx)
- **エンコーディング**: UTF-8（文字化けなし）

### カラム定義

| 列名 | 内容 |
|------|------|
| 都道府県 | 例：茨城県 |
| 順位 | 数値（1,2,3…） |
| 不用品回収業者名 | 業者名 |
| 総合評価 | 星評価 or 数値 |
| 概要 | 業者説明文（2〜3文） |
| サービス内容 | 箇条書きを「/」区切りで連結 |
| 対応エリア | 都道府県名を「,」区切り |
| 料金 | パック料金を文字列で連結 |

## ローカル実行方法

### 1. 依存パッケージのインストール

```bash
cd scripts
pip install -r requirements.txt
playwright install chromium
playwright install-deps
```

### 2. スクリプト実行

```bash
python scrape_fuyouhincenter.py
```

### 3. 出力確認

実行後、`fuyouhincenter_all_prefectures.xlsx` が生成されます。

## GitHub Actions による自動実行

### 手動実行

1. GitHubリポジトリの「Actions」タブを開く
2. 「Scrape Fuyouhin Center Data」ワークフローを選択
3. 「Run workflow」ボタンをクリック

### 自動実行スケジュール

- **頻度**: 毎週月曜日 午前3時（JST）
- **設定ファイル**: `.github/workflows/scrape.yml`

### 実行結果の確認

1. 「Actions」タブで実行履歴を確認
2. 完了したワークフローをクリック
3. 「Artifacts」セクションから `fuyouhincenter-data` をダウンロード

## 技術仕様

### 使用技術

- **Python**: 3.11
- **Playwright**: ヘッドレスブラウザ自動化（JavaScript描画対応）
- **pandas**: データ処理
- **openpyxl**: Excel出力

### スクレイピング仕様

- **対象URL**: `https://fuyouhincenter.jp/{都道府県スラッグ}/`
- **待機時間**: 各ページ読み込み後3秒待機
- **User-Agent**: Chrome 120相当
- **エラー処理**: 取得失敗しても処理継続

### 都道府県リスト

47都道府県のスラッグは以下の通り：

```python
{
    "北海道": "hokkaido",
    "青森県": "aomori",
    "岩手県": "iwate",
    # ... 以下省略
    "沖縄県": "okinawa"
}
```

## 注意事項

### 法的・倫理的配慮

⚠️ **重要**: このツールの使用には以下の責任が伴います：

1. **利用規約の遵守**: 対象サイトの利用規約を必ず確認してください
2. **著作権の尊重**: 収集したデータの利用は著作権法を遵守してください
3. **サーバー負荷への配慮**: 過度なアクセスを避け、適切な間隔を設けてください
4. **データの正確性**: スクレイピングしたデータは定期的に検証してください

### 免責事項

このスクリプトの使用による一切の責任は使用者が負うものとします。

## トラブルシューティング

### エラー: `playwright._impl._api_types.TimeoutError`

**原因**: ページ読み込みがタイムアウト

**対処法**:
```python
# scrape_fuyouhincenter.py の timeout 値を増やす
await page.goto(url, wait_until="networkidle", timeout=120000)  # 60秒→120秒
```

### エラー: 業者データが見つからない

**原因**: サイトのHTML構造が変更された

**対処法**:
1. 対象サイトをブラウザで開く
2. 開発者ツールでHTML構造を確認
3. `scrape_fuyouhincenter.py` のセレクタを更新

### Excel出力時の文字化け

**原因**: エンコーディングの問題

**対処法**:
- 必ず `engine="openpyxl"` を指定
- CSV出力は使用しない

## データの活用方法

### 1. WordPressへのインポート

```php
// WP All Import プラグインを使用
// Excel → CSV変換 → インポート
```

### 2. データベースへの投入

```python
import pandas as pd
from sqlalchemy import create_engine

df = pd.read_excel("fuyouhincenter_all_prefectures.xlsx")
engine = create_engine("mysql://user:pass@localhost/db")
df.to_sql("companies", engine, if_exists="replace", index=False)
```

### 3. AI分析

```python
# データ分析例
df = pd.read_excel("fuyouhincenter_all_prefectures.xlsx")

# 都道府県別の業者数
print(df.groupby("都道府県").size())

# 平均評価の算出
df["総合評価_数値"] = df["総合評価"].str.extract(r"(\d+\.?\d*)").astype(float)
print(df.groupby("都道府県")["総合評価_数値"].mean())
```

## 更新履歴

- **2025-01-04**: 初版作成

## ライセンス

このスクリプトはMITライセンスの下で提供されます。

---

## 比較ページ（wastecollection-comp）スクレイピング

`https://fuyouhincenter.jp/wastecollection-comp/` の全ページを巡回し、
各業者カード（`div.gyosha_results_container`）の情報をExcelに出力します。

### 追加スクリプト

```
scripts/
└── scrape_wastecollection_comp.py
```

### 出力ファイル

- **ファイル名**: `fuyouhincenter_wastecollection_comp.xlsx`
- **形式**: Excel (.xlsx)

### 取得カラム

| 列名 | 内容 |
|------|------|
| ページ | ページ番号（1,2,3...） |
| 順位 | ページ内の表示順 |
| 会社ID | `div.gyosha_results_container` の `id` |
| 不用品回収業者名 | 業者名 |
| 会社URL | 詳細ページURL |
| 公式ホームページURL | 会社ページ内の公式ホームページURL |
| ロゴ画像URL | 画像URL |
| ロゴ画像ALT | 画像代替テキスト |
| 総合評価（星） | `data-rate` の値 |
| 総合評価（テキスト） | 表示されている数値評価文字列 |
| 口コミ件数 | 口コミ数 |
| 概要 | 説明文 |
| サービス内容 | 箇条書き（/区切り） |
| 対応エリア | 都道府県一覧（/区切り） |
| 料金 | 料金プラン（/区切り） |
| 口コミタイトル | 最新口コミの見出し |
| 口コミユーザー | 例：60代・女性 |
| 口コミ評価 | 最新口コミの評価 |
| 口コミ内訳 | 評価の内訳文字列 |
| 口コミ本文 | 最新口コミ本文 |
| 口コミ画像URL | 口コミの画像URL |

### 実行方法

```bash
cd scripts
pip install -r requirements.txt
playwright install chromium
python scrape_wastecollection_comp.py
```
