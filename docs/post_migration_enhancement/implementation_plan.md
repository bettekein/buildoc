# 実装計画: 機能不足の解消と完全なCRUD実装

## 目的
Filament削除後の機能不足（スーパー管理者のテナント管理、テナント管理者のマスタ管理・案件管理・帳票出力）を解消し、業務アプリとして完全に機能する状態にする。

## 優先実装項目

### 1. 案件管理 (Projects) の修正 [緊急]
- **現状**: 「新規案件」ボタンが反応しない。
- **対応**:
    - Livewireコンポーネントに `create` アクションまたはモーダル表示を実装。
    - 作成フォーム (顧客選択、案件名、工期等) の実装。

### 2. スーパー管理者機能 (Super Admin)
- **テナント管理 (Tenants)**:
    - 一覧・作成・編集・削除 (CRUD)。
- **ユーザー管理 (Users)**:
    - テナント紐付けユーザーの管理。
    - ロール割り当て (Tenant Admin vs User)。

### 3. テナント管理者・マスタ管理 (Master Data)
- **スタッフ管理 (Staff)**: 職種、資格等の管理。
- **車両管理 (Vehicles)**: 車両番号、車検日等の管理。
- **工具管理 (Tools)**: 工具名、点検日等の管理。
- **得意先管理 (Customers)**: 見積・請求の宛先。

### 4. 案件詳細・帳票出力 (Application Flow)
- **見積作成 (Quotations)**: 既存Livewireコンポーネントの復旧とルーティング。
- **請求管理 (Billings)**: 出来高請求の実装復旧。
- **安全書類 (Green Files)**:
    - 作業者名簿、持込機械届などのPDF出力機能の再実装 (Controllerベース)。

## スケジュール (反復開発)

1.  **Phase 1: 案件作成の正常化**
    - 「新規案件」ボタンの修正。
    - `ProjectQuotations` コンポーネントの拡張。
    - 顧客マスタ (`CustomerManager`) の作成 (案件作成に必須のため)。

2.  **Phase 2: スーパー管理者機能**
    - `Admin/TenantManager` 作成。
    - `Admin/UserManager` 作成。

3.  **Phase 3: テナントマスタ機能**
    - `StaffManager`, `VehicleManager`, `ToolManager` 作成。

4.  **Phase 4: 見積・請求・帳票**
    - `QuotationEditor` の確認と修正。
    - PDF出力コントローラーの整備。

## 検証方針
各フェーズ完了ごとにChromeブラウザで以下の動作を確認する。
- ボタンクリック等のイベント発火。
- データの保存・反映 (DB)。
- 権限によるアクセス制御 (Super Admin vs Tenant)。
