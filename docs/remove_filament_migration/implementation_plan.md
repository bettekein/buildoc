# 実装計画: Filament削除とLivewire移行

## 1. 依存関係の整理
- **削除**: `filament/filament` および関連するプラグイン。
- **追加/確認**:
    - `livewire/livewire`
    - `laravel/breeze` (Dev)
    - `opcodesio/log-viewer`
    - `intervention/image` (画像処理用)
    - `wildside/userstamps`, `owen-it/laravel-auditing` (既存機能維持)

## 2. コードベースのクリーンアップ
- `app/Filament` ディレクトリの削除。
- `app/Providers/Filament` 関連の削除。
- `bootstrap/providers.php` から `AdminPanelProvider`, `AppPanelProvider` を除外。
- モデル (`User.php`) から `FilamentUser`, `HasTenants` インターフェースとトレイトを削除。

## 3. UI/UXの再構築 (Breeze + Livewire)
- **認証**: Breezeの標準スカフォールドを使用 (`php artisan breeze:install livewire`)。
- **レイアウト**: `layouts/app.blade.php` をベースにナビゲーションをカスタマイズ。
- **ナビゲーション**:
    - 案件一覧 (`/projects`) へのリンクを追加。
    - スーパー管理者 (`Super Admin`) 専用の `Log Viewer` リンクを追加。

## 4. 機能実装
### 4.1 案件一覧 (ProjectQuotations)
- `App\Livewire\ProjectQuotations` コンポーネントを作成。
- `Project` モデルからデータを取得 (ページネーション、ソート、検索)。
- 顧客名 (`customer->name`) の表示におけるNull安全性を確保 (`?? '（顧客なし）'`)。

### 4.2 ログ閲覧 (Log Viewer)
- `opcodesio/log-viewer` をインストール。
- `AppServiceProvider` にて `Gate::define('viewLogViewer', ...)` を定義し、スーパー管理者のみにアクセスを制限。

## 5. ルーティング (`routes/web.php`)
- ルートURL (`/`) をログインページ (`/login`) へリダイレクトするよう設定。
- `/projects` ルートを定義し、`auth` ミドルウェアで保護。
- `log-viewer` はパッケージのデフォルトルートを使用 (Gateによる保護)。

## 6. 検証計画
- **Unit/Feature Test**: 既存テストが通るか確認 (今回はE2E優先)。
- **Browser Test (Chrome)**:
    - Adminユーザーでのログイン、全案件表示、Log Viewerアクセス確認。
    - 一般ユーザーでのログイン、自テナント案件表示、Log Viewer非表示確認。
