<?php

use App\Http\Controllers\Reception\CallLogController;
use App\Http\Controllers\Reception\ComplaintActionController;
use App\Http\Controllers\Reception\ComplaintController;
use App\Http\Controllers\Reception\CorrespondenceController;
use App\Http\Controllers\Reception\EnquiryActionController;
use App\Http\Controllers\Reception\EnquiryController;
use App\Http\Controllers\Reception\EnquiryFollowUpController;
use App\Http\Controllers\Reception\EnquiryImportController;
use App\Http\Controllers\Reception\GatePassController;
use App\Http\Controllers\Reception\VisitorLogActionController;
use App\Http\Controllers\Reception\VisitorLogController;
use Illuminate\Support\Facades\Route;

// Reception Routes
Route::prefix('reception')->name('reception.')->group(function () {
    Route::get('enquiries/pre-requisite', [EnquiryController::class, 'preRequisite'])->name('enquiries.preRequisite');

    Route::post('enquiries/import', EnquiryImportController::class)->middleware('permission:enquiry:create');

    Route::get('enquiries/{enquiry}/follow-ups/pre-requisite', [EnquiryFollowUpController::class, 'preRequisite'])->name('enquiries.preRequisite');
    Route::apiResource('enquiries.follow-ups', EnquiryFollowUpController::class)->only(['store', 'destroy'])->names('enquiries.follow-ups');

    Route::post('enquiries/{enquiry}/records/{record}/registration', [EnquiryActionController::class, 'convertToRegistration'])->name('enquiries.convertToRegistration');

    Route::apiResource('enquiries', EnquiryController::class);

    Route::get('visitor-logs/pre-requisite', [VisitorLogController::class, 'preRequisite'])->name('visitor-logs.preRequisite');
    Route::post('visitor-logs/{visitor_log}/exit', [VisitorLogActionController::class, 'markExit'])->name('visitor-logs.markExit');
    Route::apiResource('visitor-logs', VisitorLogController::class);

    Route::get('gate-passes/pre-requisite', [GatePassController::class, 'preRequisite'])->name('gate-passes.preRequisite');
    Route::apiResource('gate-passes', GatePassController::class);

    Route::get('complaints/pre-requisite', [ComplaintController::class, 'preRequisite'])->name('complaints.preRequisite');

    Route::post('complaints/{complaint}/assign', [ComplaintActionController::class, 'assign']);
    Route::post('complaints/{complaint}/unassign/{employee}', [ComplaintActionController::class, 'unassign']);
    Route::post('complaints/{complaint}/logs', [ComplaintActionController::class, 'addLog']);
    Route::post('complaints/{complaint}/logs/{log}', [ComplaintActionController::class, 'removeLog']);

    Route::apiResource('complaints', ComplaintController::class);

    Route::get('call-logs/pre-requisite', [CallLogController::class, 'preRequisite'])->name('call-logs.preRequisite');
    Route::apiResource('call-logs', CallLogController::class);

    Route::get('correspondences/pre-requisite', [CorrespondenceController::class, 'preRequisite'])->name('correspondences.preRequisite');
    Route::apiResource('correspondences', CorrespondenceController::class);
});
