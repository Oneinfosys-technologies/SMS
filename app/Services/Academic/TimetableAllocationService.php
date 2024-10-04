<?php

namespace App\Services\Academic;

use App\Enums\Day;
use App\Http\Resources\Academic\SubjectResource;
use App\Http\Resources\Asset\Building\RoomResource;
use App\Models\Academic\Subject;
use App\Models\Academic\Timetable;
use App\Models\Academic\TimetableAllocation;
use App\Models\Academic\TimetableRecord;
use App\Models\Asset\Building\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TimetableAllocationService
{
    public function preRequisite(Timetable $timetable): array
    {
        $days = Day::getOptions();

        $subjects = Subject::query()
            ->withSubjectRecord($timetable->batch_id, $timetable->batch->course_id)
            ->orderBy('subjects.position', 'asc')
            ->get();

        $rooms = RoomResource::collection(Room::query()
            ->withFloorAndBlock()
            ->notAHostel()
            ->get());

        return [
            'days' => $days,
            'rooms' => $rooms,
            'subjects' => SubjectResource::collection($subjects),
        ];
    }

    public function allocation(Request $request, Timetable $timetable): void
    {
        $timetableRecords = TimetableRecord::query()
            ->with('classTiming.sessions')
            ->where('timetable_id', $timetable->id)
            ->get();

        $subjects = Subject::query()
            ->withSubjectRecord($timetable->batch_id, $timetable->batch->course_id)
            ->get();

        \DB::beginTransaction();

        foreach ($request->days as $day) {
            $dayName = Arr::get($day, 'value');
            $timetableRecord = $timetableRecords->where('day', $dayName)->first();

            $classTiming = $timetableRecord->classTiming;

            foreach (Arr::get($day, 'sessions', []) as $session) {
                $classTimingSession = $classTiming->sessions->where('uuid', Arr::get($session, 'uuid'))->first();

                if ($classTimingSession->is_break) {
                    continue;
                }

                $timetableAllocation = TimetableAllocation::firstOrCreate([
                    'timetable_record_id' => $timetableRecord->id,
                    'class_timing_session_id' => $classTimingSession->id,
                ]);

                $roomId = Arr::get($session, 'room_id');

                $subject = $subjects->where('uuid', Arr::get($session, 'subject.uuid'))->first();

                $timetableAllocation->room_id = $roomId;
                $timetableAllocation->subject_id = $subject?->id;
                $timetableAllocation->save();
            }
        }

        \DB::commit();
    }
}
