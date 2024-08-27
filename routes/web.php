<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ResidentialCompanyController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ServiceController;


Route::get('/', [InvoiceController::class, 'index'])->middleware(['auth', 'verified'])->name('invoices.index');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/invoices/bulk-action', [InvoiceController::class, 'bulkAction'])->name('invoices.bulk_action');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::post('/invoices/mark_paid_with_date', [InvoiceController::class, 'markPaidWithDate'])->name('invoices.mark_paid_with_date');
    Route::post('/invoices/generate-monthly', [InvoiceController::class, 'generateMonthlyInvoices'])->name('invoices.generate_monthly');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'downloadPDF'])->name('invoices.download_pdf');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    Route::get('/residential-companies', [ResidentialCompanyController::class, 'index'])->name('residential-companies.index');
    Route::post('/residential-companies', [ResidentialCompanyController::class, 'store'])->name('residential-companies.store');
    Route::get('/residential-companies/{residential_company}/edit', [ResidentialCompanyController::class, 'edit'])->name('residential-companies.edit');
    Route::put('/residential-companies/{residential_company}', [ResidentialCompanyController::class, 'update'])->name('residential-companies.update');
    Route::delete('/residential-companies/{residential_company}', [ResidentialCompanyController::class, 'destroy'])->name('residential-companies.destroy');

    Route::resource('places', PlaceController::class)->middleware('auth');

    Route::post('/services/{place_id}', [ServiceController::class, 'store'])->name('services.store');
    Route::resource('services', ServiceController::class);

    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export/download-statistics', [ExportController::class, 'downloadStatistics'])->name('export.download_statistics');

    //Route::get('/export/company/{companyId}', [ExportController::class, 'getCompanyStatistics']);

});

require __DIR__.'/auth.php';