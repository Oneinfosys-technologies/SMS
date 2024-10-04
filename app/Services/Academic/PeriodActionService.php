<?php

namespace App\Services\Academic;

use App\Models\Academic\Batch;
use App\Models\Academic\Course;
use App\Models\Academic\Division;
use App\Models\Academic\Period;
use App\Models\Academic\Subject;
use App\Models\Finance\FeeConcession;
use App\Models\Finance\FeeGroup;
use App\Models\Finance\FeeHead;
use App\Models\Transport\Circle;
use App\Models\Transport\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PeriodActionService
{
    public function select(Request $request, Period $period): void
    {
        $user = \Auth::user();

        if ($user->getPreference('academic.period_id') && $user->current_period_id == $period->id) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        if (! in_array($period->id, config('config.academic.periods', []))) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_input')]);
        }

        $preference = $user->preference;
        $preference['academic']['period_id'] = $period->id;
        $user->preference = $preference;
        $user->save();
    }

    public function default(Request $request, Period $period): void
    {
        if ($period->is_default) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        Period::query()
            ->byTeam()
            ->update(['is_default' => false]);

        $period->update(['is_default' => true]);
    }

    public function import(Request $request, Period $period): void
    {
        $this->replicateDivision($request, $period);

        $this->replicateCourse($request, $period);

        $this->replicateBatch($request, $period);

        $this->replicateSubject($request, $period);

        $this->replicateFeeGroup($request, $period);

        $this->replicateFeeHead($request, $period);

        $this->replicateFeeConcession($request, $period);

        $this->replicateTransportCircle($request, $period);

        $this->replicateTransportFee($request, $period);
    }

    private function replicateDivision(Request $request, Period $period): void
    {
        if (! $request->boolean('division') && ! $request->boolean('course') && ! $request->boolean('batch') && ! $request->boolean('subject')) {
            return;
        }

        $divisions = Division::query()
            ->where('period_id', $request->period_id)
            ->get();

        \DB::beginTransaction();
        foreach ($divisions as $division) {
            Division::query()
                ->where('period_id', $period->id)
                ->where('name', $division->name)
                ->firstOr(function () use ($division, $period) {
                    $newDivision = $division->replicate();
                    $newDivision->uuid = (string) Str::uuid();
                    $newDivision->period_id = $period->id;
                    $newDivision->period_start_date = null;
                    $newDivision->period_end_date = null;
                    $newDivision->save();
                });
        }
        \DB::commit();
    }

    private function replicateCourse(Request $request, Period $period)
    {
        if (! $request->boolean('course') && ! $request->boolean('batch') && ! $request->boolean('subject')) {
            return;
        }

        $courses = Course::query()
            ->whereHas('division', function ($q) use ($request) {
                $q->where('period_id', $request->period_id);
            })
            ->get();

        $newDivisions = Division::query()
            ->where('period_id', $period->id)
            ->get();

        \DB::beginTransaction();
        foreach ($courses as $course) {
            Course::query()
                ->whereHas('division', function ($q) use ($period, $course) {
                    $q->where('period_id', $period->id)
                        ->where('name', $course->division->name);
                })
                ->where('name', $course->name)
                ->firstOr(function () use ($course, $newDivisions) {
                    $division = $newDivisions->where('name', $course->division->name)->first();

                    if (! $division) {
                        throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('academic.division.division')])]);
                    }

                    $newCourse = $course->replicate();
                    $newCourse->uuid = (string) Str::uuid();
                    $newCourse->division_id = $division->id;
                    $newCourse->period_start_date = null;
                    $newCourse->period_end_date = null;
                    $newCourse->save();
                });
        }
        \DB::commit();
    }

    private function replicateBatch(Request $request, Period $period)
    {
        if (! $request->boolean('batch') && ! $request->boolean('subject')) {
            return;
        }

        $batches = Batch::query()
            ->whereHas('course', function ($q) use ($request) {
                $q->whereHas('division', function ($q) use ($request) {
                    $q->where('period_id', $request->period_id);
                });
            })
            ->get();

        $newCourses = Course::query()
            ->whereHas('division', function ($q) use ($period) {
                $q->where('period_id', $period->id);
            })
            ->get();

        \DB::beginTransaction();
        foreach ($batches as $batch) {
            Batch::query()
                ->whereHas('course', function ($q) use ($period, $batch) {
                    $q->whereHas('division', function ($q) use ($period) {
                        $q->where('period_id', $period->id);
                    })
                        ->where('name', $batch->course->name);
                })
                ->where('name', $batch->name)
                ->firstOr(function () use ($batch, $newCourses) {
                    $course = $newCourses->where('name', $batch->course->name)->first();

                    if (! $course) {
                        throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('academic.course.course')])]);
                    }

                    $newBatch = $batch->replicate();
                    $newBatch->uuid = (string) Str::uuid();
                    $newBatch->course_id = $course->id;
                    $newBatch->period_start_date = null;
                    $newBatch->period_end_date = null;
                    $newBatch->save();
                });
        }
        \DB::commit();
    }

    private function replicateSubject(Request $request, Period $period)
    {
        if (! $request->boolean('subject')) {
            return;
        }

        $subjects = Subject::query()
            ->where('period_id', $request->period_id)
            ->get();

        \DB::beginTransaction();
        foreach ($subjects as $subject) {
            Subject::query()
                ->where('period_id', $period->id)
                ->where('name', $subject->name)
                ->firstOr(function () use ($subject, $period) {
                    $newSubject = $subject->replicate();
                    $newSubject->uuid = (string) Str::uuid();
                    $newSubject->period_id = $period->id;
                    $newSubject->save();
                });
        }
        \DB::commit();
    }

    private function replicateFeeGroup(Request $request, Period $period)
    {
        if (! $request->boolean('fee_group') && ! $request->boolean('fee_head') && ! $request->boolean('fee_concession')) {
            return;
        }

        $feeGroups = FeeGroup::query()
            ->where('period_id', $request->period_id)
            ->get();

        \DB::beginTransaction();
        foreach ($feeGroups as $feeGroup) {
            FeeGroup::query()
                ->where('period_id', $period->id)
                ->where('name', $feeGroup->name)
                ->firstOr(function () use ($feeGroup, $period) {
                    $newFeeGroup = $feeGroup->replicate();
                    $newFeeGroup->uuid = (string) Str::uuid();
                    $newFeeGroup->period_id = $period->id;
                    $newFeeGroup->save();
                });
        }
        \DB::commit();
    }

    private function replicateFeeHead(Request $request, Period $period)
    {
        if (! $request->boolean('fee_head') && ! $request->boolean('fee_concession')) {
            return;
        }

        $feeHeads = FeeHead::query()
            ->whereHas('group', function ($q) use ($request) {
                $q->where('period_id', $request->period_id)
                    ->where(function ($q) {
                        $q->where('meta->is_custom', false)
                            ->orWhereNull('meta->is_custom');
                    });
            })
            ->get();

        $newFeeGroups = FeeGroup::query()
            ->where('period_id', $period->id)
            ->get();

        \DB::beginTransaction();
        foreach ($feeHeads as $feeHead) {
            FeeHead::query()
                ->whereHas('group', function ($q) use ($period, $feeHead) {
                    $q->where('period_id', $period->id)
                        ->where('name', $feeHead->group->name);
                })
                ->where('name', $feeHead->name)
                ->firstOr(function () use ($feeHead, $newFeeGroups, $period) {
                    $feeGroup = $newFeeGroups->where('name', $feeHead->group->name)->first();

                    if (! $feeGroup) {
                        throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('finance.fee_group.fee_group')])]);
                    }

                    $newFeeHead = $feeHead->replicate();
                    $newFeeHead->uuid = (string) Str::uuid();
                    $newFeeHead->fee_group_id = $feeGroup->id;
                    $newFeeHead->period_id = $period->id;
                    $newFeeHead->save();
                });
        }
        \DB::commit();
    }

    private function replicateFeeConcession(Request $request, Period $period)
    {
        if (! $request->boolean('fee_concession')) {
            return;
        }

        $feeConcessions = FeeConcession::query()
            ->with('records.head')
            ->where('period_id', $request->period_id)
            ->get();

        $feeHeads = FeeHead::query()
            ->whereHas('group', function ($q) use ($period) {
                $q->where('period_id', $period->id);
            })
            ->get();

        \DB::beginTransaction();
        foreach ($feeConcessions as $feeConcession) {
            FeeConcession::query()
                ->where('period_id', $period->id)
                ->where('name', $feeConcession->name)
                ->firstOr(function () use ($feeConcession, $period, $feeHeads) {
                    $newFeeConcession = $feeConcession->replicate();
                    $newFeeConcession->uuid = (string) Str::uuid();
                    $newFeeConcession->period_id = $period->id;
                    $newFeeConcession->save();

                    foreach ($feeConcession->records as $record) {
                        $feeHead = $feeHeads->where('name', $record->head->name)->first();

                        $newFeeConcessionRecord = $record->replicate();
                        $newFeeConcessionRecord->uuid = (string) Str::uuid();
                        $newFeeConcessionRecord->fee_concession_id = $newFeeConcession->id;
                        $newFeeConcessionRecord->fee_head_id = $feeHead->id;
                        $newFeeConcessionRecord->save();
                    }
                });
        }
        \DB::commit();
    }

    private function replicateTransportCircle(Request $request, Period $period)
    {
        if (! $request->boolean('transport_circle') && ! $request->boolean('transport_fee')) {
            return;
        }

        $transportCircles = Circle::query()
            ->where('period_id', $request->period_id)
            ->get();

        \DB::beginTransaction();
        foreach ($transportCircles as $transportCircle) {
            Circle::query()
                ->where('period_id', $period->id)
                ->where('name', $transportCircle->name)
                ->firstOr(function () use ($transportCircle, $period) {
                    $newTransportCircle = $transportCircle->replicate();
                    $newTransportCircle->uuid = (string) Str::uuid();
                    $newTransportCircle->period_id = $period->id;
                    $newTransportCircle->save();
                });
        }
        \DB::commit();
    }

    private function replicateTransportFee(Request $request, Period $period)
    {
        if (! $request->boolean('transport_fee')) {
            return;
        }

        $transportFees = Fee::query()
            ->with('records.circle')
            ->where('period_id', $request->period_id)
            ->get();

        $transportCircles = Circle::query()
            ->where('period_id', $period->id)
            ->get();

        \DB::beginTransaction();
        foreach ($transportFees as $transportFee) {
            Fee::query()
                ->where('period_id', $period->id)
                ->where('name', $transportFee->name)
                ->firstOr(function () use ($transportFee, $period, $transportCircles) {
                    $newTransportFee = $transportFee->replicate();
                    $newTransportFee->uuid = (string) Str::uuid();
                    $newTransportFee->period_id = $period->id;
                    $newTransportFee->save();

                    foreach ($transportFee->records as $record) {
                        $transportCircle = $transportCircles->where('name', $record->circle->name)->first();

                        $newTransportFeeRecord = $record->replicate();
                        $newTransportFeeRecord->uuid = (string) Str::uuid();
                        $newTransportFeeRecord->transport_fee_id = $newTransportFee->id;
                        $newTransportFeeRecord->transport_circle_id = $transportCircle->id;
                        $newTransportFeeRecord->save();
                    }
                });
        }
        \DB::commit();
    }
}
