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

## 確認方法
1. 案件一覧から「手配」ボタンをクリック
2. 「スタッフ配置」タブでスタッフを追加・配置解除
3. 「車両配置」「工具配置」タブでも同様の操作を確認
4. 職長フラグをクリックしてトグルできることを確認
