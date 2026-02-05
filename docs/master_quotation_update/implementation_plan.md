# 実装計画: マスタ・見積・請求機能の整備

## 概要
本セッションでは、テナント管理者が日常業務で使用する「マスタ管理」および「見積作成」機能を実装した。
引き続き、請求管理および安全書類（作業員名簿等）の実装を行う必要がある。

## 実装状況の更新

### 1. マスタ管理 [完了]
- **CustomerManager**: 実装・検証完了。
- **StaffManager**: 実装・検証完了。
- **VehicleManager**: 実装・検証完了。
- **ToolManager**: 実装・検証完了。
- **Navigation**: 全マスタへのアクセスを追加済み。

### 2. 案件・見積・請求 [進行中]
- **Project Create**: 正常化完了。
- **Quotation**: 
    - `QuotationManager` (Livewire) 実装完了。
    - PDF出力 (DomPDF) 実装完了。
    - ルーティング設定完了。
- **Billing (請求)**: **[未着手]**
    - `BillingManager` の実装が必要。
    - 見積からのデータ連携を考慮する必要がある。

### 3. 安全書類 (Green Files) [未着手]
- 作業員名簿の出力には、案件ごとのスタッフ配置 (`ProjectStaff`) が必要となる可能性が高い。

### 4. スーパー管理者機能 [未着手]
- テナント追加、ユーザー追加機能。

## 次期フェーズの実装計画

### Phase 5: 請求機能 (Billing)
1.  `BillingManager` コンポーネントの作成。
    - `QuotationManager` をベースにするが、請求日、請求金額、出来高率を入力可能にする。
2.  `BillingPdfController` の作成。
    - 請求書レイアウトの実装。
3.  案件詳細画面 (`ProjectQuotations`) への「請求」ボタン開放。

### Phase 6: 安全書類 (Safety Docs)
1.  `ProjectStaff` コンポーネント作成 (案件への入場者登録)。
2.  `SafetyDocController` 作成 (PDF出力)。
    - 作業員名簿、持込機械届など。

### Phase 7: スーパー管理者 (Tenant Admin)
1.  `/admin/tenants` のCRUD実装。
2.  ユーザー招待フローの整備。
