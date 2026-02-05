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

## 次のステップ (Phase 2: 施工体制台帳)
- [ ] 案件への下請業者配置機能の実装
  - [ ] 中間テーブル `project_subcontractors` への保存処理
  - [ ] 配置管理画面へのタブ追加
- [ ] 施工体制台帳のExcel出力
- [ ] 下請負業者編成表のExcel出力

## その後の予定 (Phase 3: 請求書強化)
- [ ] 請求書作成機能の拡充（インボイス対応、工種マスター）
