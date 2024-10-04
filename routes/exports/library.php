<?php

use App\Http\Controllers\Library\BookAdditionExportController;
use App\Http\Controllers\Library\BookExportController;
use App\Http\Controllers\Library\TransactionExportController;
use Illuminate\Support\Facades\Route;

Route::get('library/books/export', BookExportController::class)->middleware('permission:book:export');

Route::get('library/book-additions/export', BookAdditionExportController::class)->middleware('permission:book-addition:export');

Route::get('library/transactions/export', TransactionExportController::class)->middleware('permission:book:issue');
