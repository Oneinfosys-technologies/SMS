<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student\Student;
use App\Services\Student\StudentActionService;
use Illuminate\Http\Request;

class StudentActionController extends Controller
{
    public function __construct()
    {
        //
    }

    public function setDefaultPeriod(Request $request, string $student, StudentActionService $service)
    {
        $student = Student::findByUuidOrFail($student);

        $this->authorize('update', $student);

        $service->setDefaultPeriod($request, $student);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('student.student')]),
        ]);
    }

    public function updateTags(Request $request, string $student, StudentActionService $service)
    {
        $student = Student::findByUuidOrFail($student);

        $this->authorize('update', $student);

        $service->updateTags($request, $student);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('student.student')]),
        ]);
    }
}
