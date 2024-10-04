<?php

namespace App\Services\Academic;

use App\Models\Incharge;
use Illuminate\Http\Request;

class CourseInchargeService
{
    public function preRequisite(Request $request)
    {
        return [];
    }

    public function create(Request $request): Incharge
    {
        \DB::beginTransaction();

        $courseIncharge = Incharge::forceCreate($this->formatParams($request));

        \DB::commit();

        return $courseIncharge;
    }

    private function formatParams(Request $request, ?Incharge $courseIncharge = null): array
    {
        $formatted = [
            'model_type' => 'Course',
            'model_id' => $request->course_id,
            'employee_id' => $request->employee_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'remarks' => $request->remarks,
        ];

        if (! $courseIncharge) {
            //
        }

        return $formatted;
    }

    public function update(Request $request, Incharge $courseIncharge): void
    {
        \DB::beginTransaction();

        $courseIncharge->forceFill($this->formatParams($request, $courseIncharge))->save();

        \DB::commit();
    }

    public function deletable(Incharge $courseIncharge): void
    {
        //
    }
}
