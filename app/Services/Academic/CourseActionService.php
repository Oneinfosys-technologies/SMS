<?php

namespace App\Services\Academic;

use App\Models\Academic\Batch;
use App\Models\Academic\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CourseActionService
{
    public function updateConfig(Request $request, Course $course): void
    {
        //
    }

    public function reorder(Request $request): void
    {
        $courses = $request->courses ?? [];

        $allCourses = Course::query()
            ->byPeriod()
            ->get();

        foreach ($courses as $index => $courseItem) {
            $course = $allCourses->firstWhere('uuid', Arr::get($courseItem, 'uuid'));

            if (! $course) {
                continue;
            }

            $course->position = $index + 1;
            $course->save();
        }
    }

    public function reorderBatch(Request $request): void
    {
        $course = Course::query()
            ->byPeriod()
            ->where('uuid', $request->course)
            ->firstOrFail();

        $batches = Batch::query()
            ->whereCourseId($course->id)
            ->get();

        foreach ($request->batches as $index => $batchItem) {
            $batch = $batches->firstWhere('uuid', Arr::get($batchItem, 'uuid'));

            if (! $batch) {
                continue;
            }

            $batch->position = $index + 1;
            $batch->save();
        }
    }
}
