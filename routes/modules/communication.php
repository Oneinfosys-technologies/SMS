<?php

use App\Http\Controllers\Communication\AnnouncementController;
use App\Http\Controllers\Communication\EmailController;
use Illuminate\Support\Facades\Route;

// Communication Routes

Route::prefix('communication')->name('communication.')->group(function () {
    Route::get('announcements/pre-requisite', [AnnouncementController::class, 'preRequisite'])->name('announcements.preRequisite');
    Route::apiResource('announcements', AnnouncementController::class);

    Route::get('emails/pre-requisite', [EmailController::class, 'preRequisite'])->name('emails.preRequisite');
    Route::apiResource('emails', EmailController::class)->only(['index', 'store', 'show']);
});
