# ウォークスルー: マスタ管理と見積機能の実装

本セッションで行った変更内容の解説です。

## 1. マスタ管理機能の実装
以下の3つのLivewireコンポーネントとビューを作成し、データベースへの保存・一覧表示・モーダル編集を可能にしました。
- `App\Livewire\Masters\StaffManager` (スタッフマスタ)
- `App\Livewire\Masters\VehicleManager` (車両マスタ)
- `App\Livewire\Masters\ToolManager` (工具マスタ)

また、`routes/web.php` にルートを追加し、`navigation.blade.php` に「マスタ管理」ドロップダウンを追加してアクセスできるようにしました。

## 2. 顧客管理と案件作成
- `Customers\Manager` を実装し、顧客の登録を可能にしました。
- 案件作成フォーム (`Projects\Create`) で登録済みの顧客を選択できるように修正しました。

## 3. 見積作成機能 (Quotation Manager)
案件詳細画面から「見積」ボタンをクリックすることでアクセスできる見積エディタを整備しました。
- **機能**:
    - 大項目 (Section) と 明細 (Detail) の階層構造に対応。
    - 数量・単価・原価の入力と、合計金額・粗利の自動計算。
    - データの保存。
- **修正ファイル**:
    - `app/Livewire/QuotationManager.php`: ロジック実装。
    - `resources/views/livewire/quotation-manager.blade.php`: UI実装 (パンくずリストのエラー回避のため一時的にコメントアウト)。
    - `app/Http/Controllers/QuotationPdfController.php`: DomPDFを使用したPDF出力処理に変更。

## 4. ルーティングと設定
- `routes/web.php` に必要なルート定義を追加しました。
- `project-quotations.blade.php` のボタンリンクを有効化しました。
- キャッシュによる 500 エラーや 404 エラーを回避するため、 `php artisan optimize:clear` を実行しました。

## 確認方法
1. 管理画面にログインし、ナビゲーションバーの「マスタ管理」から各マスタ登録画面へアクセスできます。
2. 「案件一覧」から任意の案件を選び、「見積」ボタンを押すと見積作成画面が開きます。
3. 見積作成画面右上の「PDF出力」ボタンから見積書PDFをダウンロードできます。
