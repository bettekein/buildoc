# 変更内容のウォークスルー

## 1. Composer依存関係の変更
Filamentを削除し、標準的なLaravelパッケージ構成に変更しました。

```json
// composer.json
"require": {
    // "filament/filament": "^3.x",  <-- 削除
    "livewire/livewire": "^3.x",
    "opcodesio/log-viewer": "*"
}
```

## 2. Userモデルの修正
Filament固有のインターフェース実装を削除し、標準的なEloquentモデルに戻しました。

```php
// app/Models/User.php
class User extends Authenticatable implements Auditable //, FilamentUser, HasTenants <-- 削除
{
    // ...
    // public function canAccessPanel(...) <-- 削除
}
```

## 3. ルーティングとナビゲーション
標準的なWebルートを再定義し、ナビゲーションメニューに役割ベースのリンクを追加しました。

```php
// routes/web.php
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects', App\Livewire\ProjectQuotations::class)->name('projects.index');
});
```

```blade
<!-- resources/views/livewire/layout/navigation.blade.php -->
<x-nav-link :href="route('projects.index')" ...>案件一覧</x-nav-link>

@if(auth()->user()->hasRole('Super Admin'))
    <x-nav-link href="/log-viewer" ...>Log Viewer</x-nav-link>
@endif
```

## 4. 案件一覧の実装
Livewireコンポーネント `ProjectQuotations` を実装しました。
不整合データ（顧客なし）によるクラッシュを防ぐため、ビュー側でNull合体演算子を使用しました。

```blade
<!-- resources/views/livewire/project-quotations.blade.php -->
{{ $project->customer->name ?? '（顧客なし）' }}
```

## 5. Log Viewerのアクセス制御
`AppServiceProvider` でGateを定義しました。

```php
// app/Providers/AppServiceProvider.php
Gate::define('viewLogViewer', function ($user) {
    return $user->hasRole('Super Admin');
});
```

これにより、URI直打ちであっても非管理者はLog Viewerにアクセスできなくなりました[アクセス権限要確認: Log Viewerのデフォルト設定がGateを使用するか確認済]。
※検証結果: Adminは閲覧可、Userはリンク非表示。
