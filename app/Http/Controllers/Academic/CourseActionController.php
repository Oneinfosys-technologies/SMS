<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\Course;
use App\Services\Academic\CourseActionService;
use Illuminate\Http\Request;

class CourseActionController extends Controller
{
    public function updateConfig(Request $request, string $course, CourseActionService $service)
    {
        $course = Course::findByUuidOrFail($course);

        $this->authorize('update', $course);

        $service->updateConfig($request, $course);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('academic.course.course')]),
        ]);
    }

    public function reorder(Request $request, CourseActionService $service)
    {
        $this->authorize('create', Course::class);

        $menu = $service->reorder($request);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('academic.course.course')]),
        ]);
    }

    public function reorderBatch(Request $request, CourseActionService $service)
    {
        $this->authorize('create', Course::class);

        $menu = $service->reorderBatch($request);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('academic.course.course')]),
        ]);
    }
}
