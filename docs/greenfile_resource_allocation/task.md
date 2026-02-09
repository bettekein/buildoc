# タスク管理: 案件リソース配置機能（グリーンファイル基盤）

## 達成された目標
- [x] データベース設計とマイグレーション作成（`project_staff`, `project_vehicles`, `project_tools`）
- [x] モデルへのリレーション定義（`Project`, `Staff`, `Vehicle`, `Tool`）
- [x] 案件リソース管理用Livewireコンポーネントの作成
- [x] スタッフ配置タブの実装
- [x] 車両・工具配置タブの実装
- [x] 案件詳細画面へのリソース管理機能の統合
- [x] 動作確認（配置・解除・データ保存）
- [x] グリーンファイル（安全書類）Excel出力機能の実装
  - [x] 作業員名簿 (WorkerRosterExport)
  - [x] 持込機械届・車両 (VehicleMachineryExport)
  - [x] 持込機械届・工具 (ToolEquipmentExport)
  - [x] 出力ボタンをUIに統合

- [x] スタッフマスターの情報拡充（作業員名簿に必要な全項目）
- [x] 追加グリーンファイル実装（Phase 1）
  - [x] 社会保険加入状況
  - [x] 新規入場者調査票
- [x] 下請業者マスターの実装 (Phase 2 Start)
  - [x] `Subcontractor` モデルとマイグレーション
  - [x] 管理画面（Livewire）の実装
- [x] 案件への下請業者配置機能の実装 (Phase 2 Completed)
  - [x] 中間テーブル `project_subcontractors` への保存処理
  - [x] 配置管理画面へのタブ追加
- [x] 施工体制台帳のExcel出力
- [x] 下請負業者編成表のExcel出力

## 追加実装: 自社情報・詳細マスタ管理 (Phase 2.5)
- [x] 自社情報設定画面 (`/settings/company`) の実装
- [x] 下請業者マスタの入力項目拡充（建設業許可詳細、社会保険）
- [x] 施工体制台帳出力へのデータ連携

## その後の予定 (Phase 3: 請求書強化)
- [x] 工種マスタの実装 (Work Category Master)
  - [x] マスタ管理画面の作成
  - [x] 見積書作成画面(`QuotationEditor`)への連携
- [ ] 請求書作成機能の拡充
  - [ ] インボイス制度対応（適格請求書発行事業者番号の印字、消費税計算）
  - [ ] 内訳請求書の実装（工種ごとの出来高入力）
