# 実装計画: 案件リソース配置機能（グリーンファイル基盤）

## 概要
グリーンファイル（安全書類）の出力に向けた基盤として、案件（Project）に対してスタッフ（Staff）、車両（Vehicle）、工具（Tool）を紐付ける「リソース配置機能」を実装する。

## データベース設計

### 中間テーブル

1.  **project_staff**
    *   `id`: Primary Key
    *   `project_id`: Foreign Key (projects)
    *   `staff_id`: Foreign Key (staff)
    *   `is_foreman`: Boolean (職長フラグ)
    *   `role`: String, Nullable (役割・担当業務)
    *   `start_date`: Date, Nullable (入場開始日)
    *   `end_date`: Date, Nullable (入場終了日)
    *   `timestamps`

2.  **project_vehicles**
    *   `id`: Primary Key
    *   `project_id`: Foreign Key (projects)
    *   `vehicle_id`: Foreign Key (vehicles)
    *   `start_date`: Date, Nullable (使用開始日)
    *   `end_date`: Date, Nullable (使用終了日)
    *   `timestamps`

3.  **project_tools**
    *   `id`: Primary Key
    *   `project_id`: Foreign Key (projects)
    *   `tool_id`: Foreign Key (tools)
    *   `start_date`: Date, Nullable (使用開始日)
    *   `end_date`: Date, Nullable (使用終了日)
    *   `timestamps`

## モデル変更

*   **Project**: `staff`, `vehicles`, `tools` への `belongsToMany` リレーションを追加（`withPivot` を使用）。
*   **Staff, Vehicle, Tool**: `projects` への `belongsToMany` リレーションを追加。

## UI/UX 実装方針

*   **配置画面**: 案件詳細ページに「手配管理（Resources）」タブを追加、もしくは専用の管理画面を作成。
*   **Livewireコンポーネント**: `Projects/ResourceDispatcher` 等を作成。
*   **操作性**:
    *   左側にマスターリスト、右側に【配置済みリスト】を表示するようなUI、または複数選択可能なモーダルUIを採用。
    *   今回は実装速度とスマホ対応を考慮し、**「追加モーダル」形式**でリストから選択して追加し、一覧表示で削除・詳細編集（期間や役割）を行う形式とする。

## ファイル構成案
*   `database/migrations/xxxx_xx_xx_create_project_allocations_table.php`
*   `app/Models/Project.php` (Update)
*   `app/Models/Staff.php` (Update)
*   `app/Livewire/Projects/AllocationManager.php` (New)
*   `resources/views/livewire/projects/allocation-manager.blade.php` (New)

## Phase 2: 下請業者管理と帳票出力（完了）
- [x] 下請業者マスター (`Subcontractor`) の作成
- [x] CRUD操作の実装（Livewire）
- [x] 関連テーブル (`project_subcontractors` - 階層構造付き) の作成
- [x] 案件への下請業者配置UIの実装
- [x] **帳票出力機能の実装**
  - 施工体制台帳 (Excel)
  - 下請負業者編成表 (Excel)
- [x] **マスタ入力拡充 (Phase 2.5)**
  - 自社情報設定画面の追加
  - 下請マスタへの社会保険情報追加
  - Exportクラスの安定化（500エラー修正）

## Phase 3: 請求書強化（進行中）
- [x] **工種マスタの実装**
  - モデル (`MasterWorkCategory`) 作成
  - 管理画面（一覧・編集）の実装
  - 見積書作成 (`QuotationEditor`) への入力補完連動
- [ ] **請求書・インボイス対応**
  - インボイス登録番号の印字実装
  - 内訳請求書作成機能の拡充（実装予定）
