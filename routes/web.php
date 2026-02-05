<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects', App\Livewire\ProjectQuotations::class)->name('projects.index');
    Route::get('/projects/create', App\Livewire\Projects\Create::class)->name('projects.create');
    Route::get('/projects/{project}/edit', App\Livewire\Projects\Edit::class)->name('projects.edit');
    Route::get('/customers', App\Livewire\Customers\Manager::class)->name('customers.index');

    // Masters
    Route::get('/masters/staff', App\Livewire\Masters\StaffManager::class)->name('masters.staff');
    Route::get('/masters/vehicles', App\Livewire\Masters\VehicleManager::class)->name('masters.vehicles');
    Route::get('/masters/tools', App\Livewire\Masters\ToolManager::class)->name('masters.tools');
    Route::get('/projects/{project}/quotation', App\Livewire\QuotationManager::class)->name('quotations.edit');
    Route::get('/projects/{project}/quotation/pdf', [App\Http\Controllers\QuotationPdfController::class, 'show'])->name('quotations.pdf');
    // Route::get('/projects/{project}/billings', App\Livewire\BillingManager::class)->name('billings.index');

    // Super Admin Routes (Should be protected by role middleware in production)
    Route::get('/admin/tenants', App\Livewire\Admin\TenantManager::class)->name('admin.tenants');
    Route::get('/admin/audits', App\Livewire\Admin\AuditLogViewer::class)->name('admin.audits');
});

require __DIR__ . '/auth.php';
