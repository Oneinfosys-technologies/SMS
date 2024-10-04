<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\RegistrationRequest;
use App\Http\Requests\Student\RegistrationUpdateRequest;
use App\Http\Resources\Student\RegistrationResource;
use App\Models\Student\Registration;
use App\Services\Student\RegistrationListService;
use App\Services\Student\RegistrationService;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function preRequisite(RegistrationService $service)
    {
        return response()->ok($service->preRequisite());
    }

    public function index(Request $request, RegistrationListService $service)
    {
        $this->authorize('viewAny', Registration::class);

        return $service->paginate($request);
    }

    public function store(RegistrationRequest $request, RegistrationService $service)
    {
        $this->authorize('create', Registration::class);

        $registration = $service->create($request);

        return response()->success([
            'message' => trans('global.created', ['attribute' => trans('student.registration.registration')]),
            'registration' => RegistrationResource::make($registration),
        ]);
    }

    public function show(string $registration, RegistrationService $service): RegistrationResource
    {
        $registration = Registration::findByUuidOrFail($registration);

        $this->authorize('view', $registration);

        $registration->load(['period', 'course.division', 'contact' => function ($q) {
            $q->withGuardian()->with('guardian');
        }, 'transactions' => function ($q) {
            $q->withPayment();
        }, 'admission.batch', 'media']);

        return RegistrationResource::make($registration);
    }

    public function update(RegistrationUpdateRequest $request, string $registration, RegistrationService $service)
    {
        $registration = Registration::findByUuidOrFail($registration);

        $this->authorize('update', $registration);

        $service->update($request, $registration);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('student.registration.registration')]),
        ]);
    }

    public function destroy(string $registration, RegistrationService $service)
    {
        $registration = Registration::findByUuidOrFail($registration);

        $this->authorize('delete', $registration);

        $service->deletable($registration);

        $registration->delete();

        return response()->success([
            'message' => trans('global.deleted', ['attribute' => trans('student.registration.registration')]),
        ]);
    }

    public function downloadMedia(string $registration, string $uuid, RegistrationService $service)
    {
        $registration = Registration::findByUuidOrFail($registration);

        $this->authorize('view', $registration);

        return $registration->downloadMedia($uuid);
    }
}
