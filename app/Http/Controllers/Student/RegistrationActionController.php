<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\RegistrationActionRequest;
use App\Models\Student\Registration;
use App\Services\Student\RegistrationActionService;
use Illuminate\Http\Request;

class RegistrationActionController extends Controller
{
    public function preRequisite(Request $request, Registration $registration, RegistrationActionService $service)
    {
        $this->authorize('action', $registration);

        return response()->ok($service->preRequisite($request, $registration));
    }

    public function action(RegistrationActionRequest $request, Registration $registration, RegistrationActionService $service)
    {
        $this->authorize('action', $registration);

        $service->action($request, $registration);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('student.registration.registration')]),
        ]);
    }
}
