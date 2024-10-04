<?php

namespace App\Services\Academic;

use App\Models\Incharge;
use Illuminate\Http\Request;

class SubjectInchargeService
{
    public function preRequisite(Request $request)
    {
        return [];
    }

    public function create(Request $request): Incharge
    {
        \DB::beginTransaction();

        $subjectIncharge = Incharge::forceCreate($this->formatParams($request));

        \DB::commit();

        return $subjectIncharge;
    }

    private function formatParams(Request $request, ?Incharge $subjectIncharge = null): array
    {
        $formatted = [
            'model_type' => 'Subject',
            'model_id' => $request->subject_id,
            'detail_type' => 'Batch',
            'detail_id' => $request->batch_id,
            'employee_id' => $request->employee_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'remarks' => $request->remarks,
        ];

        if (! $subjectIncharge) {
            //
        }

        return $formatted;
    }

    public function update(Request $request, Incharge $subjectIncharge): void
    {
        \DB::beginTransaction();

        $subjectIncharge->forceFill($this->formatParams($request, $subjectIncharge))->save();

        \DB::commit();
    }

    public function deletable(Incharge $subjectIncharge): void
    {
        //
    }
}
