# 請求書PDF発行機能 実装タスク

- [x] **500エラー (Internal Server Error) の解消**
  - [x] `BillingEditor` コンポーネントの修正 (Nullプロパティアクセス回避)
  - [x] Bladeファイル (`BillingEditor`, `Dashboard`) 内の未定義ルート参照の修正
  - [x] ビューキャッシュのクリア

- [x] **請求書PDF発行機能の実装**
  - [x] `spatie/browsershot` への移行 (DomPDFからの変更)
  - [x] `BillingPdfController` の修正 (Browsershot使用)
  - [x] PDF用 Blade View (`billings.pdf`) のフォント設定調整
  - [x] `routes/web.php` 設定

- [x] **UI/UX の調整**
  - [x] `BillingEditor` にPDF発行ボタンの追加
  - [x] パンくずリスト (`Breadcrumbs`) の整備
