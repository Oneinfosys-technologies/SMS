<?php

namespace App\Actions\Employee\Payroll;

use App\Enums\Employee\Attendance\Category as AttendanceCategory;
use App\Models\Employee\Attendance\Attendance;
use Illuminate\Support\Collection;

class GetAttendanceSummary
{
    public function execute(Attendance $attendance, Collection $attendanceTypes): array
    {
        $attendances = [];

        array_push($attendances, [
            'code' => 'L',
            'name' => trans('employee.leave.leave'),
            'count' => $attendance?->L ?? 0,
            'color' => 'danger',
            'unit' => 'days',
        ]);

        array_push($attendances, [
            'code' => 'LWP',
            'name' => trans('employee.leave.leave_without_pay_short'),
            'count' => $attendance?->LWP ?? 0,
            'color' => 'warning',
            'unit' => 'days',
        ]);

        foreach ($attendanceTypes as $attendanceType) {
            $attendanceCode = $attendanceType->code;
            array_push($attendances, [
                'code' => $attendanceType->code,
                'name' => $attendanceType->name,
                'count' => $attendance?->$attendanceCode ?? 0,
                'color' => AttendanceCategory::getColor($attendanceType->category->value),
                'unit' => 'days',
            ]);
        }

        return $attendances;
    }
}
