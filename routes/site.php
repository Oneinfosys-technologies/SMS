<?php

use App\Http\Controllers\Site\BookListController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('academic/book-lists/{course}', BookListController::class);

if (config('config.site.enable_site') && config('config.site.theme') != 'custom') {
    Route::get('/', [SiteController::class, 'home'])->name('site.home');
    Route::get('/pages/{slug}', [SiteController::class, 'page'])->name('site.page');
}
