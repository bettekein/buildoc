# 変更の歩き方: マスターデータ編集とUI修正

このプルリクエスト（作業）には、マスターデータの編集機能の実装、テナント管理のUI修正、および案件情報の編集機能が含まれます。

## 主な変更点

### 1. マスターデータの編集対応
- **ファイル**: 
  - `app/Livewire/Masters/StaffManager.php`, `resources/views/livewire/masters/staff-manager.blade.php`
  - `app/Livewire/Masters/VehicleManager.php`, `resources/views/livewire/masters/vehicle-manager.blade.php`
  - `app/Livewire/Masters/ToolManager.php`, `resources/views/livewire/masters/tool-manager.blade.php`
  - `app/Livewire/Customers/Manager.php`, `resources/views/livewire/customers/manager.blade.php`
- **変更内容**:
  - `edit($id)` メソッドによるデータ読み込みとモーダル表示。
  - `save()` メソッドでの `create` と `update` の条件分岐。
  - UIへの「編集」ボタン追加とモーダルタイトルの動的変更。

### 2. テナント管理の不具合修正
- **ファイル**: `app/Livewire/Admin/TenantManager.php`
- **変更内容**:
  - `cancel()` メソッドを追加し、モーダルを閉じる際に変数を確実にリセットするように修正。これにより、次回「新規作成」を開いた際に古いデータが残らないようになりました。

### 3. 案件（プロジェクト）編集機能
- **ファイル**: 
  - `app/Livewire/Projects/Edit.php` (新規)
  - `resources/views/livewire/projects/edit.blade.php` (新規)
  - `routes/web.php`
  - `routes/breadcrumbs.php`
  - `resources/views/livewire/project-quotations.blade.php`
- **変更内容**:
  - 案件の基本情報（名称、顧客、工期）を後から変更するための画面を作成しました。
  - 案件一覧画面に「編集」ボタンを追加しました。
  - パンくずリストの設定を追加し、画面遷移のエラーを防止しました。

## 確認方法
1. 各マスター画面（スタッフ、車両、工具、顧客）で既存データの「編集」ボタンを押し、内容を変更して「更新」できることを確認。
2. テナント管理画面で「編集」モーダルを開いた後「キャンセル」し、再度「新規作成」を開いてフォームが空であることを確認。
3. 案件一覧画面から「編集」へ遷移し、案件名を変更して保存できることを確認。
