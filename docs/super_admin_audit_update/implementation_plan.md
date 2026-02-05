<h1>実装計画: マスタ管理の機能強化</h1>

## 概要
スーパー管理者機能（テナントCRUD、監査ログ）は完了した。
マスタ管理（スタッフ、車両、工具、顧客）における検索機能と論理削除（ゴミ箱）機能の実装を行う。

## 実装項目

### 1. スタッフマスタ (StaffManager) [完了]
- 検索ボックス追加。
- 「ゴミ箱を表示」トグル追加。
- 削除、復元、完全削除のアクションボタン追加。
- コンポーネントロジックの更新 (`restore`, `forceDelete`, `delete`).

### 2. 車両マスタ (VehicleManager) [未完了]
- `StaffManager` と同様の変更を適用する。
- `app/Livewire/Masters/VehicleManager.php`
- `resources/views/livewire/masters/vehicle-manager.blade.php`

### 3. 工具マスタ (ToolManager) [未完了]
- `StaffManager` と同様の変更を適用する。
- `app/Livewire/Masters/ToolManager.php`
- `resources/views/livewire/masters/tool-manager.blade.php`

### 4. 顧客マスタ (CustomerManager) [未完了]
- `StaffManager` と同様の変更を適用する。
- `app/Livewire/Customers/Manager.php`
- `resources/views/livewire/customers/manager.blade.php`

## 検証
- 各マスタ画面で検索が動作すること。
- 削除したレコードが一覧から消え、ゴミ箱モードで見えること。
- 復元できること。
- 完全削除できること。
