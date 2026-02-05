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
    Route::get('/customers', App\Livewire\Customers\Manager::class)->name('customers.index'); // Added customer route // Added route
    // Route::get('/projects/{project}/quotation', App\Livewire\QuotationManager::class)->name('quotations.edit');
    // Route::get('/projects/{project}/quotation/pdf', [App\Http\Controllers\QuotationPdfController::class, 'show'])->name('quotations.pdf');
    // Route::get('/projects/{project}/billings', App\Livewire\BillingManager::class)->name('billings.index');
    // Route::get('/projects/{project}/billings/{billing}/edit', App\Livewire\BillingEditor::class)->name('billings.edit');
    // Route::get('/projects/{project}/billings/{billing}/pdf', [App\Http\Controllers\BillingPdfController::class, 'show'])->name('billings.pdf');
});

require __DIR__ . '/auth.php';
