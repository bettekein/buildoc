# 請求書PDF発行機能 実装計画

## 目標
出来高請求書のPDFを日本のインボイス制度に対応した形式で発行・ダウンロードできるようにする。
また、請求書作成画面へのアクセス時に発生していた 500エラー を解消する。

## 実装ステップ

### 1. デバッグとエラー解消
- **問題**: 請求書作成画面 (`/projects/{id}/billings/create`) へのアクセス時に 500エラー が発生。
- **原因と対応**:
  - `BillingEditor.php` の初期化ロジック修正。
  - `breadcrumbs.php` および Blade ファイル (`billing-editor.blade.php`, `dashboard.blade.php`) から未定義ルート `billings.index` への参照を削除・置換。
  - Blade ビューキャッシュの手動クリア。

### 2. PDF生成機能の実装
- **ライブラリ**: `spatie/browsershot` (Puppeteer)
  - ※当初 `laravel-dompdf` を導入したが、日本語フォントの文字化け等のため `browsershot` に変更。
  - `npm install puppeteer` 済みを確認。
- **コントローラー**: `BillingPdfController`
  - `Browsershot::html($html)` を使用。
  - Windows環境に対応するため `setNodeBinary` を設定。
- **ビュー**: `resources/views/billings/pdf.blade.php`
  - 日本語フォント (`Yu Gothic`, `Meiryo`) を指定。
  - インボイス登録番号、消費税計算などを実装。

### 3. UI連携
- `BillingEditor` に「請求書PDF発行」ボタンを追加。

## 検証
- [x] 請求書作成画面が正常に表示されること。
- [x] 請求データを保存できること。
- [x] 「請求書PDF発行」ボタンをクリックすると PDF が生成されること。
- [x] 日本語が正しく表示されること（Browsershotによるレンダリング確認）。
