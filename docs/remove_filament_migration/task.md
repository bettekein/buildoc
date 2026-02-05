# タスク: Filamentの削除とLivewire/Breezeへの移行

## 目的
Filament依存を削除し、標準的なLaravel + Livewire + Tailwind (Breeze) スタックへ移行する。
これにより、UIの完全なコントロールを取り戻し、要件に応じたカスタマイズを容易にする。

## 要件
1.  **Filamentの完全削除**:
    *   `composer remove filament/filament` および関連パッケージ。
    *   設定ファイル、プロバイダーの削除。
    *   モデル (`User`, `Tenant`, `Project` 等) からFilament固有のトレイト/インターフェースを削除。
2.  **認証基盤の再構築**:
    *   Laravel Breeze (Livewireスタック) のインストール。
    *   ログイン画面、ダッシュボードの標準化。
3.  **機能の再実装**:
    *   **案件一覧 (Projects)**: Livewireコンポーネントとして再作成。
    *   **ログ閲覧 (Log Viewer)**: `opcodesio/log-viewer` を導入し、スーパー管理者のみアクセス可能にする。
4.  **動作検証**:
    *   ChromeでのE2Eテストによるログイン、一覧表示、権限管理の確認。

## 進捗状況
- [x] Filamentパッケージの削除
- [x] Breeze (Livewire) のインストールとセットアップ
- [x] Userモデルのクリーンアップ (Filamentトレイト削除)
- [x] ProjectQuotations Livewireコンポーネントの作成
- [x] Log Viewerのセットアップと権限設定 (Gate)
- [x] ナビゲーションメニューの更新
- [x] Chromeでの動作検証 (完了)
