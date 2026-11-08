<?php

use Illuminate\Support\Facades\Route;
use Modules\Membership\Controllers\MemberController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('members/trashed', [MemberController::class, 'trashed'])->name('members.trashed');
        Route::post('members/{id}/restore', [MemberController::class, 'restore'])->whereNumber('id')->name('members.restore');
        Route::resource('members', MemberController::class)->except(['index', 'show']);
    });

    Route::get('members', [MemberController::class, 'index'])->name('members.index');
});
