# 変更の歩き方: 案件リソース配置機能

このプルリクエスト（作業）には、案件に対してスタッフ・車両・工具を紐付ける「リソース配置機能」の実装が含まれます。これはグリーンファイル（安全書類）出力の基盤となるデータ構造です。

## 主な変更点

### 1. データベース構造（マイグレーション）
- **ファイル**: `database/migrations/2026_02_05_150454_create_project_allocations_tables.php`
- **作成テーブル**:
  - `project_staff`: 案件×スタッフの多対多中間テーブル（職長フラグ、役割、期間を含む）
  - `project_vehicles`: 案件×車両の多対多中間テーブル（使用期間を含む）
  - `project_tools`: 案件×工具の多対多中間テーブル（使用期間を含む）

### 2. モデルへのリレーション追加
- **ファイル**: `app/Models/Project.php`, `Staff.php`, `Vehicle.php`, `Tool.php`
- **変更内容**:
  - `Project` モデルに `staff()`, `vehicles()`, `tools()` リレーションを追加
  - 各マスターモデルに `projects()` 逆リレーションを追加
  - `withPivot()` を利用して中間テーブルのカラム（期間、役割等）にアクセス可能

### 3. 配置管理UI
- **ファイル**: `app/Livewire/Projects/AllocationManager.php`, `resources/views/livewire/projects/allocation-manager.blade.php`
- **機能**:
  - タブ切り替えによるスタッフ・車両・工具の管理
  - モーダルからマスターデータを選択して配置
  - 職長フラグのトグル機能
  - 配置解除機能

### 4. ルーティングとナビゲーション
- **ファイル**: `routes/web.php`, `routes/breadcrumbs.php`, `resources/views/livewire/project-quotations.blade.php`
- **変更内容**:
  - `/projects/{project}/allocations` ルートを追加
  - パンくずリスト設定を追加
  - 案件一覧に「手配」ボタンを追加

### 5. グリーンファイル出力機能 (Phase 1)
- **ファイル**: `app/Exports/*`, `app/Http/Controllers/GreenFileController.php`
- **機能**:
  - 作業員名簿、持込機械届（車両・工具）、社会保険加入状況、新規入場者調査票のExcel出力。

### 6. 下請業者管理機能 (Phase 2)
- **ファイル**: `app/Models/Subcontractor.php`, `database/migrations/*_create_subcontractors_table.php`, `app/Livewire/Masters/SubcontractorManager.php`
- **機能**:
  - 下請業者マスターのCRUD（作成・読み取り・更新・削除）。
  - 建設業許可情報の管理。
  - 案件への下請業者配置機能（階層管理付き）。
  - **New**: 施工体制台帳、下請負業者編成表のExcel出力。

### 7. 自社情報設定・機能改善
- **ファイル**: `app/Livewire/Settings/CompanyProfile.php`, `resources/views/livewire/settings/company-profile.blade.php`, `App\Models\Tenant.php`
- **機能**:
  - 自社情報（建設業許可詳細、社会保険）の詳細設定画面。
  - 500エラーの解消（Export処理の安定化）。
  - 下請業者マスター入力項目の拡充（社会保険情報の追加）。

### 8. 工種マスター (Phase 3)
- **ファイル**: `app/Models/MasterWorkCategory.php`, `app/Livewire/Masters/WorkCategoryManager.php`
- **機能**:
  - 見積書で使用する標準的な「工種」（例：仮設工事、基礎工事）を管理。
  - 見積書編集画面で入力補完として利用可能。

## 確認方法
1. **下請業者マスター**: メニュー「マスタ管理」>「下請業者マスタ」から下請業者を登録。
2. **工種マスター**: メニュー「マスタ管理」>「工種マスタ」からよく使う工種を登録。
3. **案件リソース配置**: 案件一覧 > 「手配」ボタン > 「下請業者配置」タブ。
4. **見積書作成**: 案件詳細 > 見積書タブ > 編集。工種入力時にマスタから候補が表示されることを確認。
5. **グリーンファイル**: 右上のボタンから各帳票を出力し、内容を確認。
