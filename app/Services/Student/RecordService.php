<?php

namespace App\Services\Student;

use App\Concerns\HasCodeNumber;
use App\Models\Finance\FeeAllocation;
use App\Models\Student\Student;
use App\Support\FormatCodeNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class RecordService
{
    use FormatCodeNumber, HasCodeNumber;

    public function preRequisite(Request $request): array
    {
        return [];
    }

    // public function create(Request $request, Student $student): Record
    // {
    //     \DB::beginTransaction();

    //     $contact = (new CreateContact)->execute($request->all());

    //     $guardian = Record::firstOrCreate([
    //         'contact_id' => $contact->id,
    //         'primary_contact_id' => $student->contact_id,
    //     ]);

    //     $guardian->relation = $request->relation;
    //     $guardian->save();

    //     \DB::commit();

    //     return $guardian;
    // }

    public function update(Request $request, Student $student, Student $record): void
    {
        if (! $student->isStudying()) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        \DB::beginTransaction();

        if ($request->boolean('edit_batch')) {
            $newBatch = $request->batch;

            if ($record->batch_id == $newBatch->id) {
                throw ValidationException::withMessages(['message' => trans('general.infos.nothing_to_submit')]);
            }

            $this->validateChangeBatch($request, $record);

            $record->batch_id = $newBatch->id;
        }

        if ($request->boolean('edit_code_number')) {
            $admission = $student->admission;

            $previousStudent = Student::query()
                ->whereContactId($student->contact_id)
                ->whereAdmissionId($admission->id)
                ->where('start_date', '<', $record->start_date->value)
                ->orderBy('start_date', 'desc')
                ->first();

            $nextStudent = Student::query()
                ->whereContactId($student->contact_id)
                ->whereAdmissionId($admission->id)
                ->where('start_date', '>', $record->start_date->value)
                ->orderBy('start_date', 'asc')
                ->first();

            if ($previousStudent && $request->start_date < $previousStudent->start_date->value) {
                throw ValidationException::withMessages(['message' => trans('validation.gt.numeric', ['attribute' => trans('student.record.props.promotion_date'), 'value' => $previousStudent->start_date->formatted])]);
            }

            if ($nextStudent && $request->start_date > $nextStudent->start_date->value) {
                throw ValidationException::withMessages(['message' => trans('validation.lt.numeric', ['attribute' => trans('student.record.props.promotion_date'), 'value' => $nextStudent->start_date->formatted])]);
            }

            $existingCodeNumber = Student::query()
                ->where('uuid', '!=', $student->uuid)
                ->where('contact_id', '!=', $student->contact_id)
                ->whereHas('contact', function ($q) {
                    $q->where('team_id', auth()->user()->current_team_id);
                })
                ->whereHas('admission', function ($q) use ($request) {
                    $q->where('code_number', $request->code_number);
                })
                ->first();

            if ($existingCodeNumber) {
                throw ValidationException::withMessages(['message' => trans('student.record.code_number_already_exists')]);
            }

            $admissionNumberPrefix = config('config.student.admission_number_prefix');
            $admissionNumberSuffix = config('config.student.admission_number_suffix');
            $admissionNumberDigit = config('config.student.admission_number_digit', 0);
            $admissionNumberFormat = $admissionNumberPrefix.'%NUMBER%'.$admissionNumberSuffix;

            if ($request->code_number_format) {
                $number = $this->getNumberFromFormat($request->code_number, $request->code_number_format);

                if (is_null($number)) {
                    throw ValidationException::withMessages(['message' => trans('student.record.code_number_format_mismatch')]);
                }

                $numberFormat = $request->code_number_format;
            } else {
                $number = $this->getNumberFromFormat($request->code_number, $admissionNumberFormat);

                $numberFormat = ! is_null($number) ? $admissionNumberFormat : null;
            }

            $student->start_date = $request->start_date;
            $student->save();

            if ($previousStudent) {
                $previousStudent->end_date = $request->start_date;
                $previousStudent->save();
            }

            $admission->joining_date = $request->joining_date;
            $admission->code_number = $request->code_number;
            $admission->number_format = $numberFormat;
            $admission->number = $number;
            $admission->save();
        }

        $record->remarks = $request->remarks;
        $record->save();

        \DB::commit();
    }

    private function validateChangeBatch(Request $request, Student $record)
    {
        $newBatch = $request->batch;

        if ($newBatch->course->division->period_id != $record->period_id) {
            throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('academic.batch.batch')])]);
        }

        if (! $record->fee_structure_id) {
            return;
        }

        $existingFeeAllocation = FeeAllocation::query()
            ->whereBatchId($record->batch_id)
            ->first() ?? FeeAllocation::query()
            ->whereCourseId($record->batch->course_id)
            ->first();

        $newFeeAllocation = FeeAllocation::query()
            ->whereBatchId($newBatch->id)
            ->first() ?? FeeAllocation::query()
            ->whereCourseId($newBatch->course_id)
            ->first();

        if ($existingFeeAllocation->fee_structure_id == $newFeeAllocation->fee_structure_id) {
            return;
        }

        throw ValidationException::withMessages(['message' => trans('student.record.could_not_change_batch_with_different_fee_allocation')]);
        // $paidStudentFees = Fee::query()
        //     ->whereStudentId($record->id)
        //     ->where('paid', '>', 0)
        //     ->count();

        // if ($paidStudentFees) {
        //     throw ValidationException::withMessages(['message' => trans('student.record.could_not_change_batch_with_different_fee_allocation')]);
        // }
    }

    private function resetFee(Student $student)
    {
        foreach ($student->fees as $studentFee) {
            $studentFee->setMeta([
                'total_before_cancel' => $studentFee->total->value,
            ]);
            $studentFee->total = $studentFee->paid->value;
            $studentFee->save();

            foreach ($studentFee->records as $record) {
                $record->setMeta([
                    'amount_before_cancel' => $record->amount->value,
                ]);
                $record->amount = $record->paid->value;
                $record->save();
            }
        }
    }

    public function cancelAdmission(Request $request, Student $student)
    {
        if (! $student->isStudying()) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        $student->load('admission');

        if ($student->start_date->value != $student->admission->joining_date->value) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        $nextRecord = Student::query()
            ->where('uuid', '!=', $student->uuid)
            ->where('contact_id', $student->contact_id)
            ->where('start_date', '>', $student->start_date->value)
            ->exists();

        if ($nextRecord) {
            throw ValidationException::withMessages(['message' => trans('student.record.could_not_cancel_previous_admission')]);
        }

        $feeSummary = $student->getFeeSummary();

        $paidFee = Arr::get($feeSummary, 'paid_fee');

        if ($paidFee->value) {
            throw ValidationException::withMessages(['message' => trans('student.record.could_not_cancel_if_paid')]);
        }

        \DB::beginTransaction();

        $admission = $student->admission;
        $admission->setMeta([
            'previous_record' => [
                'number' => $admission->number,
                'number_format' => $admission->number_format,
                'code_number' => $admission->code_number,
            ],
        ]);
        $admission->cancelled_at = now()->toDateTimeString();
        $admission->number = null;
        $admission->number_format = null;
        $admission->code_number = null;
        $admission->save();

        $student->cancelled_at = now()->toDateTimeString();
        $student->save();

        $this->resetFee($student);

        \DB::commit();
    }

    public function cancelPromotion(Request $request, Student $student)
    {
        if (! $student->isStudying()) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        $student->load('admission');

        if ($student->start_date->value == $student->admission->joining_date->value) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        $nextRecord = Student::query()
            ->where('uuid', '!=', $student->uuid)
            ->where('contact_id', $student->contact_id)
            ->where('start_date', '>', $student->start_date->value)
            ->exists();

        if ($nextRecord) {
            throw ValidationException::withMessages(['message' => trans('student.record.could_not_cancel_previous_admission')]);
        }

        $feeSummary = $student->getFeeSummary();

        $paidFee = Arr::get($feeSummary, 'paid_fee');

        if ($paidFee->value) {
            throw ValidationException::withMessages(['message' => trans('student.record.could_not_cancel_if_paid')]);
        }

        $previousStudentId = $student->getMeta('previous_student_id');

        \DB::beginTransaction();

        Student::query()
            ->where('id', $previousStudentId)
            ->update(['end_date' => null]);

        $student->cancelled_at = now()->toDateTimeString();
        $student->save();

        $this->resetFee($student);

        \DB::commit();
    }

    // public function deletable(Student $student, Record $guardian): void
    // {
    //     //
    // }
}
