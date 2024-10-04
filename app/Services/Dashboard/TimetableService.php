<?php

namespace App\Services\Dashboard;

use App\Http\Resources\Employee\EmployeeSummaryResource;
use App\Models\Academic\Batch;
use App\Models\Academic\ClassTiming;
use App\Models\Academic\ClassTimingSession;
use App\Models\Academic\Subject;
use App\Models\Academic\Timetable;
use App\Models\Academic\TimetableAllocation;
use App\Models\Asset\Building\Room;
use App\Models\Employee\Employee;
use App\Models\Incharge;
use App\Models\Student\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimetableService
{
    public function fetch(Request $request)
    {
        if (! auth()->user()->hasAnyRole(['student', 'guardian'])) {
            return $this->fetchForEmployee($request);
        }

        $students = Student::query()
            ->byPeriod()
            ->record()
            ->filterForStudentAndGuardian()
            ->get();

        $timetables = Timetable::query()
            ->with('records.allocations')
            ->whereIn('batch_id', $students->pluck('batch_id')->all())
            ->get();

        $rooms = Room::query()
            ->withFloorAndBlock()
            ->notAHostel()
            ->get();

        $classTimings = ClassTiming::query()
            ->with('sessions')
            ->byPeriod()
            ->get();

        $rows = [];

        foreach ($students as $student) {
            $student->load('batch.course');
            $batch = $student->batch;
            $subjects = Subject::query()
                ->withSubjectRecord($batch->id, $batch->course_id)
                ->get();

            $timetable = $timetables->where('batch_id', $batch->id)
                ->where('effective_date.value', '<=', today()->toDateString())
                ->sortByDesc('effective_date.value')
                ->first();

            $row = [
                'name' => $student->name,
                'batch' => $batch->course->name . ' ' . $batch->name,
                'info' => null,
                'sessions' => [],
            ];

            if (! $timetable) {
                $row['info'] = trans('global.could_not_find', ['attribute' => trans('academic.timetable.timetable')]);
                $rows[] = $row;
                continue;
            }

            $room = $rooms->firstWhere('id', $timetable->room_id);

            if ($room) {
                $row['room'] = $room->full_name;
            }

            $timetableRecord = $timetable->records->firstWhere('day', strtolower(today()->format('l')));

            if (! $timetableRecord) {
                $row['info'] = trans('academic.timetable.timetable_not_found_for_today');
                $rows[] = $row;
                continue;
            }

            if ($timetableRecord->is_holiday) {
                $row['info'] = trans('academic.timetable.holiday_info', ['attribute' => today()->format('l')]);
                $rows[] = $row;
                continue;
            }

            $classTiming = $classTimings->where('id', $timetableRecord->class_timing_id)->first();

            if (! $classTiming) {
                $row['info'] = trans('academic.timetable.class_timing_not_found');
                $rows[] = $row;
                continue;
            }

            $timetableAllocations = $timetableRecord->allocations;

            $sessions = [];
            foreach ($classTiming->sessions as $session) {
                $allocation = $timetableAllocations->firstWhere('class_timing_session_id', $session->id);

                $isCurrent = Carbon::parse($session->start_time->value)->lessThanOrEqualTo(now()) && Carbon::parse($session->end_time->value)->greaterThanOrEqualTo(now());

                if (! $allocation) {
                    $sessions[] = [
                        'name' => $session->name,
                        'start_time' => $session->start_time,
                        'end_time' => $session->end_time,
                        'is_current' => $isCurrent,
                        'subject' => null,
                        'employee' => [],
                        'room' => null,
                    ];

                    continue;
                }

                $subject = $subjects->where('id', $allocation->subject_id)->first();

                $room = $rooms->firstWhere('id', $allocation->room_id);

                $sessions[] = [
                    'name' => $session->name,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'is_current' => $isCurrent,
                    'subject' => [
                        'name' => $subject?->name,
                        'code' => $subject?->code,
                    ],
                    'employee' => [],
                    'room' => $room?->full_name,
                ];
            }

            $row['sessions'] = $sessions;
            $rows[] = $row;
        }

        return $rows;
    }

    private function fetchForEmployee(Request $request)
    {
        $employee = Employee::query()
            ->auth()
            ->first();

        if (! $employee) {
            return [];
        }

        $subjectIncharges = Incharge::query()
            ->where('employee_id', $employee->id)
            ->where('model_type', 'Subject')
            ->where('detail_type', 'Batch')
            ->get();

        if ($subjectIncharges->isEmpty()) {
            return [];
        }

        $rooms = Room::query()
            ->withFloorAndBlock()
            ->notAHostel()
            ->get();

        $subjects = Subject::query()
            ->whereIn('id', $subjectIncharges->pluck('model_id')->all())
            ->get();

        $batches = Batch::query()
            ->with('course')
            ->whereIn('id', $subjectIncharges->pluck('detail_id')->all())
            ->get();

        $timetables = Timetable::query()
            ->whereIn('batch_id', $batches->pluck('id')->all())
            ->get();

        $latestTimetableId = [];
        foreach ($batches as $batch) {
            $timetable = $timetables->where('batch_id', $batch->id)
                ->where('effective_date.value', '<=', today()->toDateString())
                ->sortByDesc('effective_date.value')
                ->first();

            $latestTimetableId[] = $timetable->id;
        }

        $timetableAllocations = TimetableAllocation::query()
            ->select('timetable_allocations.*', 'timetables.batch_id')
            ->join('timetable_records', 'timetable_allocations.timetable_record_id', '=', 'timetable_records.id')
            ->join('timetables', 'timetable_records.timetable_id', '=', 'timetables.id')
            ->where('timetable_records.day', strtolower(today()->format('l')))
            ->whereIn('timetable_records.timetable_id', $latestTimetableId)
            ->whereIn('subject_id', $subjects->pluck('id')->all())
            ->get();

        $classTimingSessions = ClassTimingSession::query()
            ->whereIn('id', $timetableAllocations->pluck('class_timing_session_id')->all())
            ->get();

        $sessions = [];
        foreach ($timetableAllocations as $timetableAllocation) {
            $classTimingSession = $classTimingSessions->where('id', $timetableAllocation->class_timing_session_id)->first();

            $subject = $subjects->where('id', $timetableAllocation->subject_id)->first();

            $room = $rooms->where('id', $timetableAllocation->room_id)->first();

            $batch = $batches->where('id', $timetableAllocation->batch_id)->first();

            $sessions[] = [
                'name' => $classTimingSession->name,
                'start_time' => $classTimingSession->start_time,
                'end_time' => $classTimingSession->end_time,
                'subject' => [
                    'name' => $subject?->name,
                    'code' => $subject?->code,
                ],
                'room' => $room?->full_name,
                'batch' => $batch?->course->name . ' ' . $batch?->name,
            ];
        }

        $employee = EmployeeSummaryResource::make(Employee::query()
            ->summary()
            ->where('employees.id', $employee->id)
            ->first());

        return [
            'sessions' => collect($sessions)->sortBy('start_time.value')->values()->all(),
            'employee' => $employee,
        ];
    }
}
