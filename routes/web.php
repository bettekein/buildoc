<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/dashboard', App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects', App\Livewire\ProjectQuotations::class)->name('projects.index');
    Route::get('/projects/{project}/quotation', App\Livewire\QuotationManager::class)->name('quotations.edit');
    Route::get('/projects/{project}/quotation/pdf', [App\Http\Controllers\QuotationPdfController::class, 'show'])->name('quotations.pdf');
    Route::get('/projects/{project}/billings', App\Livewire\BillingManager::class)->name('billings.index');
    Route::get('/projects/{project}/billings/{billing}/edit', App\Livewire\BillingEditor::class)->name('billings.edit');
    Route::get('/projects/{project}/billings/{billing}/pdf', [App\Http\Controllers\BillingPdfController::class, 'show'])->name('billings.pdf');
    Route::get('/audits', App\Livewire\AuditLogViewer::class)->name('audits.index');
});

require __DIR__.'/auth.php';
