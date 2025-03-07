<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\SettingsController;

Route::middleware('auth')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/view-pdf', [InvoiceController::class, 'viewPdf'])->name('invoices.view-pdf');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::resource('clients', ClientController::class);
    Route::get('/payments', [InvoiceController::class, 'payments'])->name('invoices.payments');
    Route::post('/notes', [InvoiceController::class, 'storeNote'])->name('notes.store');
    Route::put('/notes/{note}', [InvoiceController::class, 'updateNote'])->name('notes.update');
    Route::delete('/notes/{note}', [InvoiceController::class, 'destroyNote'])->name('notes.destroy');
    Route::get('/company-profile', [CompanyProfileController::class, 'index'])->name('company-profile.index');
    Route::get('/company-profile/edit', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
    Route::put('/company-profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

Route::get('/invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');

Auth::routes(['register' => false]);