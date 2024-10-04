<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\RegistrationPaymentRequest;
use App\Models\Student\Registration;
use App\Services\Student\RegistrationPaymentService;
use Illuminate\Http\Request;

class RegistrationPaymentController extends Controller
{
    public function preRequisite(Request $request, Registration $registration, RegistrationPaymentService $service)
    {
        $this->authorize('action', $registration);

        return response()->ok($service->preRequisite($request, $registration));
    }

    public function skipPayment(Request $request, Registration $registration, RegistrationPaymentService $service)
    {
        $this->authorize('action', $registration);

        $service->skipPayment($request, $registration);

        return response()->success([
            'message' => trans('global.skipped', ['attribute' => trans('academic.course.props.registration_fee')]),
        ]);
    }

    public function payment(RegistrationPaymentRequest $request, Registration $registration, RegistrationPaymentService $service)
    {
        $this->authorize('action', $registration);

        $service->payment($request, $registration);

        return response()->success([
            'message' => trans('global.paid', ['attribute' => trans('academic.course.props.registration_fee')]),
        ]);
    }

    public function cancelPayment(Request $request, Registration $registration, $uuid, RegistrationPaymentService $service)
    {
        $this->authorize('action', $registration);

        $service->cancelPayment($request, $registration, $uuid);

        return response()->success([
            'message' => trans('global.cancelled', ['attribute' => trans('academic.course.props.registration_fee')]),
        ]);
    }
}
