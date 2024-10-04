<?php

namespace App\Actions\Exam;

use App\Models\Academic\Batch;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ProcessCumulativeMarksheet
{
    public function execute(Batch $batch, Collection $students, array $params)
    {
        throw ValidationException::withMessages(['message' => trans('general.errors.feature_under_development')]);
        $params['subject_absent_criteria'] = 'all'; // all | any
        $params['cumulative_assessment'] = false;

        $allStudents = $students;
    }
}
