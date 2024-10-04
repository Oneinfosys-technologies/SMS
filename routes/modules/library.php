<?php

use App\Http\Controllers\Library\BookAdditionController;
use App\Http\Controllers\Library\BookController;
use App\Http\Controllers\Library\BookCopyController;
use App\Http\Controllers\Library\BookImportController;
use App\Http\Controllers\Library\TransactionActionController;
use App\Http\Controllers\Library\TransactionController;
use Illuminate\Support\Facades\Route;

// Library Routes
Route::prefix('library')->middleware('permission:library:config')->group(function () {});

Route::prefix('library')->name('library.')->group(function () {
    Route::get('books/pre-requisite', [BookController::class, 'preRequisite']);
    Route::post('books/import', BookImportController::class)->middleware('permission:book:create');
    Route::apiResource('books', BookController::class);

    Route::get('book-additions/pre-requisite', [BookAdditionController::class, 'preRequisite']);
    Route::apiResource('book-additions', BookAdditionController::class);

    Route::apiResource('book-copies', BookCopyController::class)->only(['index']);

    Route::get('transactions/pre-requisite', [TransactionController::class, 'preRequisite']);
    Route::post('transactions/{book_issue}/return', [TransactionActionController::class, 'returnBook'])->name('transactions.returnBook');
    Route::apiResource('transactions', TransactionController::class);
});
