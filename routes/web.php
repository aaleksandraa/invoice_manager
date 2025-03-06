<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;

Route::middleware('auth')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/view-pdf', [InvoiceController::class, 'viewPdf'])->name('invoices.view-pdf'); // Nova ruta za prikaz
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::resource('clients', ClientController::class);
    Route::get('/payments', [InvoiceController::class, 'payments'])->name('invoices.payments');
    Route::post('/notes', [InvoiceController::class, 'storeNote'])->name('notes.store');
    Route::put('/notes/{note}', [InvoiceController::class, 'updateNote'])->name('notes.update');
    Route::delete('/notes/{note}', [InvoiceController::class, 'destroyNote'])->name('notes.destroy');
});

Route::get('/invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');

Auth::routes(['register' => false]);