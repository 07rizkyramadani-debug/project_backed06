<?php

use Illuminate\Support\Facades\Route;
use Modules\Circulation\Controllers\LoanController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('loans/trashed', [LoanController::class, 'trashed'])->name('loans.trashed');
        Route::post('loans/{id}/restore', [LoanController::class, 'restore'])->whereNumber('id')->name('loans.restore');
        Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
    });

    Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('loans/{loan}', [LoanController::class, 'show'])->whereNumber('loan')->name('loans.show');
    Route::post('loans/{loan}/return', [LoanController::class, 'returnLoan'])->whereNumber('loan')->name('loans.return');
});
