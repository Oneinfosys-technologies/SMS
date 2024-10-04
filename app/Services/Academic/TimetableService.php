<?php

namespace App\Services\Academic;

use App\Enums\Day;
use App\Http\Resources\Academic\ClassTimingResource;
use App\Http\Resources\Academic\TimetableResource;
use App\Http\Resources\Asset\Building\RoomResource;
use App\Models\Academic\ClassTiming;
use App\Models\Academic\Subject;
use App\Models\Academic\Timetable;
use App\Models\Academic\TimetableAllocation;
use App\Models\Academic\TimetableRecord;
use App\Models\Asset\Building\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class TimetableService
{
    public function preRequisite(): array
    {
        $days = Day::getOptions();

        $classTimings = ClassTimingResource::collection(ClassTiming::query()
            ->with('sessions')
            ->byPeriod()
            ->get());

        $rooms = RoomResource::collection(Room::query()
            ->withFloorAndBlock()
            ->notAHostel()
            ->get());

        return compact('classTimings', 'days', 'rooms');
    }

    public function getDetail(Timetable $timetable)
    {
        $timetable->load(['batch.course', 'room' => fn ($q) => $q->withFloorAndBlock()]);

        $rooms = Room::query()
            ->withFloorAndBlock()
            ->notAHostel()
            ->get();

        $weekdays = Day::getOptions();

        $subjects = Subject::query()
            ->withSubjectRecord($timetable->batch_id, $timetable->batch->course_id)
            ->orderBy('subjects.position', 'asc')
            ->get();

        $timetableRecords = TimetableRecord::query()
            ->with('classTiming.sessions')
            ->where('timetable_id', $timetable->id)
            ->get();

        $timetableAllocations = TimetableAllocation::query()
            ->WhereIn('timetable_record_id', $timetableRecords->pluck('id')->all())
            ->get();

        $days = [];
        foreach ($weekdays as $day) {
            $timetableRecord = $timetableRecords->firstWhere('day', Arr::get($day, 'value'));

            if (! $timetableRecord) {
                continue;
            }

            if ($timetableRecord->is_holiday) {
                $days[] = [
                    'label' => Arr::get($day, 'label'),
                    'value' => Arr::get($day, 'value'),
                    'is_holiday' => true,
                    'sessions' => [],
                ];

                continue;
            }

            $startTime = $timetableRecord->classTiming->sessions->min('start_time');
            $endTime = $timetableRecord->classTiming->sessions->max('end_time');

            $duration = Carbon::parse($startTime->value)->diff(Carbon::parse($endTime->value));

            $sessions = [];
            foreach ($timetableRecord->classTiming->sessions as $session) {
                $timetableAllocation = $timetableAllocations
                    ->where('timetable_record_id', $timetableRecord->id)
                    ->where('class_timing_session_id', $session->id)
                    ->first();
                $subjectId = $timetableAllocation?->subject_id;
                $subject = $subjects->firstWhere('id', $subjectId);

                $room = $rooms->firstWhere('id', $timetableAllocation?->room_id);

                $sessions[] = [
                    'name' => $session->name,
                    'uuid' => $session->uuid,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'duration' => $session->start_time->formatted.' - '.$session->end_time->formatted,
                    'is_break' => (bool) $session->is_break,
                    'room' => $room?->uuid,
                    'room_name' => $room?->fullName,
                    'subject' => $subject ? [
                        'uuid' => $subject?->uuid,
                        'name' => $subject?->name,
                        'code' => $subject?->code,
                        'shortcode' => $subject?->shortcode,
                    ] : null,
                ];
            }

            $days[] = [
                'label' => Arr::get($day, 'label'),
                'value' => Arr::get($day, 'value'),
                'is_holiday' => false,
                'duration' => $duration->h.' '.trans('list.durations.hours').' '.$duration->i.' '.trans('list.durations.minutes'),
                'period' => $startTime->formatted.' - '.$endTime->formatted,
                'sessions' => $sessions,
            ];
        }

        $timetable->has_detail = true;
        $timetable->days = $days;

        return TimetableResource::make($timetable);
    }

    public function create(Request $request): Timetable
    {
        \DB::beginTransaction();

        $timetable = Timetable::forceCreate($this->formatParams($request));

        $this->updateRecords($request, $timetable);

        \DB::commit();

        return $timetable;
    }

    private function formatParams(Request $request, ?Timetable $timetable = null): array
    {
        $formatted = [
            'batch_id' => $request->batch_id,
            'effective_date' => $request->effective_date,
            'room_id' => $request->room_id,
            'description' => $request->description,
        ];

        if (! $timetable) {
            //
        }

        return $formatted;
    }

    public function export(Timetable $timetable)
    {
        $timetable->load('records.allocations');

        $batch = $timetable->batch;

        $subjects = Subject::query()
            ->withSubjectRecord($batch->id, $batch->course_id)
            ->get();

        $rooms = Room::query()
            ->withFloorAndBlock()
            ->notAHostel()
            ->get();

        $hasSameClassTiming = $timetable->records->pluck('class_timing_id')->unique()->count() === 1;

        $classTimings = ClassTiming::query()
            ->with('sessions')
            ->whereIn('id', $timetable->records->pluck('class_timing_id')->all())
            ->byPeriod()
            ->get();

        $days = [];
        $maxNoOfSessions = 0;
        foreach ($timetable->records as $record) {
            $classTiming = $classTimings->firstWhere('id', $record->class_timing_id);

            if (! $classTiming) {
                continue;
            }

            if ($maxNoOfSessions < $classTiming->sessions->count()) {
                $maxNoOfSessions = $classTiming->sessions->count();
            }

            $allocations = $record->allocations;

            $sessions = [];
            foreach ($classTiming->sessions as $session) {
                $allocation = $allocations->firstWhere('class_timing_session_id', $session->id);

                $subject = null;
                $room = null;
                if ($allocation) {
                    $subject = $subjects->firstWhere('id', $allocation->subject_id);
                    $room = $rooms->firstWhere('id', $allocation->room_id);
                }

                $sessions[] = [
                    'name' => $session->name,
                    'start_time' => \Cal::time($session->start_time),
                    'end_time' => \Cal::time($session->end_time),
                    'subject' => $subject,
                    'room' => $room?->full_name,
                    'is_break' => $session->is_break,
                ];
            }

            $days[] = [
                'day' => Day::getDetail($record->day),
                'start_time' => \Cal::time($classTiming?->sessions?->min('start_time')),
                'end_time' => \Cal::time($classTiming?->sessions?->max('end_time')),
                'is_holiday' => $record->is_holiday,
                'sessions' => $sessions,
                'filler_session' => $maxNoOfSessions - count($sessions),
            ];
        }

        $timetable->has_same_class_timing = $hasSameClassTiming;
        $timetable->room_name = $rooms->firstWhere('id', $timetable->room_id)?->full_name;

        return view('print.academic.timetable.index', compact('timetable', 'batch', 'days'));
    }

    private function updateRecords(Request $request, Timetable $timetable): void
    {
        foreach ($request->records as $record) {
            $timetableRecord = TimetableRecord::firstOrCreate([
                'timetable_id' => $timetable->id,
                'day' => Arr::get($record, 'day'),
            ]);

            $timetableRecord->is_holiday = Arr::get($record, 'is_holiday', false);
            $timetableRecord->class_timing_id = Arr::get($record, 'class_timing_id');
            $timetableRecord->save();
        }
    }

    public function update(Request $request, Timetable $timetable): void
    {
        // $timetableRecords = TimetableRecord::query()
        //     ->whereTimetableId($timetable->id)
        //     ->get();

        // if (TimetableAllocation::query()
        //     ->whereIn('timetable_record_id', $timetableRecords->pluck('id')->all())
        //     ->exists()) {
        //     throw ValidationException::withMessages(['message' => trans('academic.timetable.could_not_modify_if_allocated')]);
        // }

        \DB::beginTransaction();

        $timetable->forceFill($this->formatParams($request, $timetable))->save();

        $this->updateRecords($request, $timetable);

        \DB::commit();
    }

    public function deletable(Timetable $timetable, $validate = false): ?bool
    {
        $timetableRecords = TimetableRecord::query()
            ->whereTimetableId($timetable->id)
            ->get();

        if (TimetableAllocation::query()
            ->whereIn('timetable_record_id', $timetableRecords->pluck('id')->all())
            ->exists()) {
            throw ValidationException::withMessages(['message' => trans('academic.timetable.could_not_modify_if_allocated')]);
        }

        return true;
    }
}
