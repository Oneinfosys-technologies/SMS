<?php

namespace App\Services\Student;

use App\Actions\Student\AssignFee;
use App\Actions\Student\AssignFeeInstallment;
use App\Actions\Student\GetStudentFees;
use App\Actions\Student\UpdateFeeInstallment;
use App\Enums\Finance\LateFeeFrequency;
use App\Enums\Transport\Direction;
use App\Http\Resources\Finance\FeeConcessionResource;
use App\Http\Resources\Transport\CircleResource;
use App\Models\Finance\FeeAllocation;
use App\Models\Finance\FeeConcession;
use App\Models\Finance\FeeInstallment;
use App\Models\Finance\FeeInstallmentRecord;
use App\Models\Student\Fee;
use App\Models\Student\Student;
use App\Models\Transport\Circle;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class FeeService
{
    public function preRequisite(Request $request, Student $student): array
    {
        $directions = Direction::getOptions();

        $transportCircles = CircleResource::collection(Circle::query()
            ->byPeriod($student->period_id)
            ->get());

        $feeConcessions = FeeConcessionResource::collection(FeeConcession::query()
            ->byPeriod($student->period_id)
            ->get());

        $feeAllocation = FeeAllocation::query()
            ->whereBatchId($student->batch_id)
            ->first() ?? FeeAllocation::query()
            ->whereCourseId($student->batch->course_id)
            ->first();

        if (! $feeAllocation) {
            throw ValidationException::withMessages(['message' => trans('finance.fee_structure.not_allocated')]);
        }

        $optionalFeeHeads = [];
        if (! $student->fee_structure_id) {
            $feeInstallmentRecords = FeeInstallmentRecord::query()
                ->with('head')
                ->whereHas('installment', function ($q) use ($feeAllocation) {
                    $q->whereFeeStructureId($feeAllocation->fee_structure_id);
                })
                ->whereIsOptional(1)
                ->get();

            $optionalFeeHeads = $feeInstallmentRecords->map(function ($feeInstallmentRecord) {
                return [
                    'name' => $feeInstallmentRecord->head->name,
                    'uuid' => $feeInstallmentRecord->head->uuid,
                ];
            });

            $optionalFeeHeads = collect($optionalFeeHeads)->unique('uuid')->values()->all();
        }

        $frequencies = LateFeeFrequency::getOptions();

        return compact('directions', 'transportCircles', 'feeConcessions', 'optionalFeeHeads', 'frequencies');
    }

    private function getFeeConcession(Student $student, ?string $feeConcessionUuid): ?FeeConcession
    {
        if (! $feeConcessionUuid) {
            return null;
        }

        return FeeConcession::query()
            ->with('records')
            ->byPeriod($student->period_id)
            ->whereUuid($feeConcessionUuid)
            ->getOrFail(trans('finance.fee_concession.fee_concession'), 'fee_concession');
    }

    private function getTransportCircle(Student $student, string $transportcircleUuid): ?Circle
    {
        if (! $transportcircleUuid) {
            return null;
        }

        return Circle::query()
            ->byPeriod($student->period_id)
            ->whereUuid($transportcircleUuid)
            ->getOrFail(trans('transport.circle.circle'), 'transport_circle');
    }

    public function setFee(Request $request, Student $student): void
    {
        if (! $student->isStudying()) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        if (Fee::whereStudentId($student->id)->count()) {
            throw ValidationException::withMessages(['message' => trans('student.fee.could_not_set_if_fee_already_set')]);
        }

        $isNewStudent = false;
        if ($student->admission->joining_date->value == $student->start_date->value) {
            $isNewStudent = true;
        }

        $feeConcession = $this->getFeeConcession($student, $request->fee_concession);

        $transportCircle = $this->getTransportCircle($student, $request->transport_circle);

        \DB::beginTransaction();

        (new AssignFee)->execute(
            student: $student,
            feeConcession: $feeConcession,
            transportCircle: $transportCircle,
            params: [
                'direction' => $request->direction,
                'opted_fee_heads' => $request->opted_fee_heads,
                'is_new_student' => $isNewStudent,
            ]
        );

        \DB::commit();
    }

    public function getFeeInstallment(Request $request, Student $student, string $uuid): Fee
    {
        $fee = Fee::query()
            ->whereStudentId($student->id)
            ->whereUuid($uuid)
            ->firstOrFail();

        return $fee;
    }

    public function updateFee(Request $request, Student $student): void
    {
        // throw ValidationException::withMessages(['message' => trans('general.errors.feature_under_development')]);

        if (! $student->isStudying()) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        $fees = Fee::query()
            ->with('installment', 'installment.group', 'concession', 'transportCircle', 'records', 'records.head')
            ->whereStudentId($student->id)
            ->get();

        $feeConcessions = FeeConcession::query()
            ->with('records')
            ->byPeriod($student->period_id)
            ->get();

        $transportCircles = Circle::query()
            ->byPeriod($student->period_id)
            ->get();

        \DB::beginTransaction();

        foreach ($request->fee_groups as $feeGroup) {
            foreach (Arr::get($feeGroup, 'fees', []) as $fee) {
                $studentFee = $fees->firstWhere('uuid', Arr::get($fee, 'uuid'));

                $feeConcession = $feeConcessions->firstWhere('uuid', Arr::get($fee, 'concession'));

                $transportCircle = $transportCircles->firstWhere('uuid', Arr::get($fee, 'transport_circle'));

                // logger($studentFee?->uuid);

                // if (! $studentFee) {
                //     $feeInstallment = FeeInstallment::query()
                //         ->with('records.head')
                //         ->whereUuid(Arr::get($fee, 'uuid'))
                //         ->first();

                //     if (! $feeInstallment) {
                //         throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('finance.fee_structure.installment')])]);
                //     }

                //     $optedFeeHeads = [];
                //     foreach ($feeInstallment->records as $record) {
                //         $inputFeeRecord = collect(Arr::get($fee, 'records', []))->firstWhere('head.uuid', $record->head->uuid);
                //         if ($record->is_optional && Arr::get($inputFeeRecord, 'is_applicable')) {
                //             $optedFeeHeads[] = $record->head->uuid;
                //         }
                //     }

                //     logger($optedFeeHeads);
                //     throw ValidationException::withMessages(['message' => 'testing']);

                // (new AssignFeeInstallment)->execute(
                //     student: $student,
                //     feeInstallment: $feeInstallment,
                //     feeConcession: $feeConcession,
                //     transportCircle: $transportCircle,
                //     params: array(
                //         'direction' => Arr::get($fee, 'direction'),
                //         'opted_fee_heads' => $optedFeeHeads,
                //     )
                // );
                //     continue;
                // }

                // throw ValidationException::withMessages(['message' => 'test']);

                // Allowing editing fee if paid is lesser than new amount
                // if ($studentFee->paid->value > 0) {
                //     continue;
                // }

                (new UpdateFeeInstallment)->execute(
                    studentFee: $studentFee,
                    feeConcession: $feeConcession,
                    transportCircle: $transportCircle,
                    params: [
                        ...$fee,
                    ]
                );
            }
        }

        \DB::commit();
    }

    public function updateFeeInstallment(Request $request, Student $student, Fee $studentFee): void
    {
        throw ValidationException::withMessages(['message' => trans('general.errors.feature_under_development')]);
        if ($studentFee->paid->value > 0) {
            throw ValidationException::withMessages(['message' => trans('student.fee.could_not_edit_if_fee_paid')]);
        }

        $feeConcession = $this->getFeeConcession($student, $request->fee_concession);

        \DB::beginTransaction();

        // (new UpdateFeeInstallment)->execute(
        //     studentFee: $studentFee,
        //     feeConcession: $feeConcession,
        //     params: [
        //         'transport_circle_id' => $request->transport_circle,
        //         ...$request->all(),
        //     ]
        // );

        \DB::commit();
    }

    public function resetFee(Student $student): void
    {
        if (! $student->isStudying()) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        if (! Fee::whereStudentId($student->id)->count()) {
            throw ValidationException::withMessages(['message' => trans('student.fee.could_not_reset_if_fee_not_set')]);
        }

        $student->load('fees');

        if ($student->fees->where('paid.value', '>', 0)->count()) {
            throw ValidationException::withMessages(['message' => trans('student.fee.could_not_reset_if_fee_paid')]);
        }

        \DB::beginTransaction();

        Fee::whereStudentId($student->id)->delete();

        $student->fee_structure_id = null;
        $student->save();

        \DB::commit();
    }

    public function getStudentFees(Student $student): array
    {
        return (new GetStudentFees)->execute($student);
    }
}
