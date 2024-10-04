<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Reception\Enquiry;
use App\Models\Reception\EnquiryRecord;
use App\Services\Reception\EnquiryActionService;
use App\Services\Student\RegistrationService;
use Illuminate\Http\Request;

class EnquiryActionController extends Controller
{
    public function convertToRegistration(Request $request, string $enquiry, string $record, EnquiryActionService $service, RegistrationService $registrationService)
    {
        $enquiry = Enquiry::findByUuidOrFail($enquiry);

        $this->authorize('action', $enquiry);

        $enquiryRecord = EnquiryRecord::query()
            ->where('enquiry_id', $enquiry->id)
            ->where('uuid', $record)
            ->firstOrFail();

        $params['code_number_detail'] = $registrationService->codeNumber($enquiryRecord->course_id);

        $service->convertToRegistration($request, $enquiry, $enquiryRecord, $params);

        return response()->success([
            'message' => trans('global.created', ['attribute' => trans('student.registration.registration')]),
        ]);
    }
}
