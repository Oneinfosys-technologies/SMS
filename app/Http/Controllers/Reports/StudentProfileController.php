<?php

namespace App\Http\Controllers\Reports;

use App\Models\Academic\Batch;
use App\Models\Incharge;
use App\Models\Student\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StudentProfileController
{
    public function __invoke(Request $request)
    {
        $batches = Batch::query()
            ->with('course')
            ->byPeriod()
            ->get();

        $incharges = Incharge::query()
            ->whereHasMorph(
                'model',
                [Batch::class],
                function (Builder $query) {
                    $query->whereNotNull('id');
                }
            )
            ->with(['employee' => fn ($q) => $q->summary()])
            ->get();

        $data = [];
        foreach ($batches as $batch) {
            $students = Student::query()
                ->select('id')
                ->whereBatchId($batch->id)
                ->whereNull('end_date')
                ->get();

            $total = $students->count();

            $missingAlternateMobile = Student::query()
                ->whereBatchId($batch->id)
                ->whereNull('end_date')
                ->whereHas('contact', function ($q) {
                    $q->whereNull('alternate_records')->orWhere('alternate_records->contact_number', '=', '');
                })
                ->count();

            $missingPhoto = Student::query()
                ->whereBatchId($batch->id)
                ->whereNull('end_date')
                ->whereHas('contact', function ($q) {
                    $q->whereNull('photo');
                })
                ->count();

            $missingAadhar = Student::query()
                ->whereBatchId($batch->id)
                ->whereNull('end_date')
                ->whereHas('contact', function ($q) {
                    $q->whereNull('unique_id_number1')->orWhere('unique_id_number1', '=', '');
                })
                ->count();

            $missingCaste = Student::query()
                ->whereBatchId($batch->id)
                ->whereNull('end_date')
                ->whereHas('contact', function ($q) {
                    $q->whereNull('caste_id');
                })
                ->count();

            $missingCategory = Student::query()
                ->whereBatchId($batch->id)
                ->whereNull('end_date')
                ->whereHas('contact', function ($q) {
                    $q->whereNull('category_id');
                })
                ->count();

            $missingReligion = Student::query()
                ->whereBatchId($batch->id)
                ->whereNull('end_date')
                ->whereHas('contact', function ($q) {
                    $q->whereNull('religion_id');
                })
                ->count();

            $batchIncharge = $incharges
                ->where('model_id', $batch->id)
                ->first();

            $data[] = [
                'batch' => $batch->course->name.' '.$batch->name,
                'total' => $total,
                'missing_alternate_number' => $missingAlternateMobile,
                'missing_photo' => $missingPhoto,
                'missing_caste' => $missingCaste,
                'missing_category' => $missingCategory,
                'missing_religion' => $missingReligion,
                'missing_aadhar' => $missingAadhar,
                // 'subjects' => implode(', ', $subjects),
                'incharge' => $batchIncharge->employee?->name,
            ];
        }

        return view('reports.student.profile', compact('data'));
    }
}
