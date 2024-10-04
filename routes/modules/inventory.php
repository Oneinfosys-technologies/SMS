<?php

use App\Http\Controllers\Inventory\InchargeController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\StockAdjustmentController;
use App\Http\Controllers\Inventory\StockCategoryController;
use App\Http\Controllers\Inventory\StockItemController;
use App\Http\Controllers\Inventory\StockPurchaseController;
use App\Http\Controllers\Inventory\StockRequisitionController;
use App\Http\Controllers\Inventory\StockTransferController;
use Illuminate\Support\Facades\Route;

// Inventory Routes
Route::middleware('permission:inventory:config')->group(function () {
    Route::get('inventories/pre-requisite', [InventoryController::class, 'preRequisite']);
    Route::apiResource('inventories', InventoryController::class);

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('incharges/pre-requisite', [InchargeController::class, 'preRequisite'])->name('incharges.preRequisite');
        Route::apiResource('incharges', InchargeController::class);
    });
});

Route::prefix('inventory')->middleware('permission:inventory:config')->group(function () {});

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('stock-categories/pre-requisite', [StockCategoryController::class, 'preRequisite']);
    Route::apiResource('stock-categories', StockCategoryController::class);

    Route::get('stock-items/pre-requisite', [StockItemController::class, 'preRequisite']);
    Route::apiResource('stock-items', StockItemController::class);

    Route::get('stock-requisitions/pre-requisite', [StockRequisitionController::class, 'preRequisite']);
    Route::apiResource('stock-requisitions', StockRequisitionController::class);

    Route::get('stock-purchases/pre-requisite', [StockPurchaseController::class, 'preRequisite']);
    Route::apiResource('stock-purchases', StockPurchaseController::class);

    Route::get('stock-transfers/pre-requisite', [StockTransferController::class, 'preRequisite']);
    Route::apiResource('stock-transfers', StockTransferController::class);

    Route::get('stock-adjustments/pre-requisite', [StockAdjustmentController::class, 'preRequisite']);
    Route::apiResource('stock-adjustments', StockAdjustmentController::class);
});
