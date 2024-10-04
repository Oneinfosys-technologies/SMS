<?php

use App\Http\Controllers\Custom\FeeMismatchController;
use App\Http\Controllers\Custom\HeadWiseFeeSummaryController;
use App\Http\Controllers\Student\PromotionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:sanctum', 'user.config', 'role:admin'])->group(function () {
    Route::get('fee-mismatch', FeeMismatchController::class);

    Route::get('head-wise-fee-summary', HeadWiseFeeSummaryController::class);

    Route::get('cancel-promotion', [PromotionController::class, 'cancel']);
});
