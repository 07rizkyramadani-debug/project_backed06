<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Controllers\AuthorController;
use Modules\Catalog\Controllers\BookController;
use Modules\Catalog\Controllers\CategoryController;

Route::middleware(['web', 'auth'])->group(function () {
    // Khusus admin: kelola buku, daftar terhapus, dan restore (didefinisikan sebelum books/{book})
    Route::middleware('role:admin')->group(function () {
        Route::get('books/trashed', [BookController::class, 'trashed'])->name('books.trashed');
        Route::post('books/{id}/restore', [BookController::class, 'restore'])->whereNumber('id')->name('books.restore');
        Route::resource('books', BookController::class)->except(['index', 'show']);
    });

    Route::resource('books', BookController::class)->only(['index', 'show']);
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('authors', [AuthorController::class, 'index'])->name('authors.index');
});
