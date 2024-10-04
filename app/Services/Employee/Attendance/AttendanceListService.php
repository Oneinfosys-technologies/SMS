<?php

namespace App\Services\Employee\Attendance;

use App\Actions\Employee\FetchEmployee;
use App\Contracts\ListGenerator;
use App\Enums\Employee\Attendance\Category as AttendanceCategory;
use App\Http\Resources\Employee\Attendance\AttendanceResource;
use App\Models\Employee\Attendance\Attendance;
use App\Models\Employee\Attendance\Type as AttendanceType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AttendanceListService extends ListGenerator
{
    public function getHeaders(Request $request): array
    {
        $headers = [
            [
                'key' => 'employee',
                'label' => trans('employee.employee'),
                'print_label' => 'name',
                'visibility' => true,
            ],
        ];

        if ($request->boolean('day_wise')) {
            return $this->getDayWiseHeaders($request, $headers);
        }

        return $this->getSummaryHeaders($request, $headers);
    }

    private function getSummaryHeaders(Request $request, array $headers): array
    {
        foreach ($request->attendance_types as $attendanceType) {
            array_push($headers, [
                'key' => 'type_'.Arr::get($attendanceType, 'code'),
                'label' => Arr::get($attendanceType, 'name'),
                'print_label' => 'summary._'.Arr::get($attendanceType, 'code'),
                'center_align' => true,
                'visibility' => true,
            ]);
        }

        array_push($headers, [
            'key' => 'type_late',
            'label' => trans('employee.attendance.sub_categories.late'),
            'print_label' => 'summary._late',
            'center_align' => true,
            'visibility' => true,
        ]);

        array_push($headers, [
            'key' => 'type_early_leaving',
            'label' => trans('employee.attendance.sub_categories.early_leaving'),
            'print_label' => 'summary._early_leaving',
            'center_align' => true,
            'visibility' => true,
        ]);

        array_push($headers, [
            'key' => 'type_overtime',
            'label' => trans('employee.attendance.sub_categories.overtime'),
            'print_label' => 'summary._overtime',
            'center_align' => true,
            'visibility' => true,
        ]);

        return $headers;
    }

    private function getDayWiseHeaders(Request $request, array $headers): array
    {
        $date = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        while ($date <= $endDate) {
            $day = $date->format('d');
            $singleDay = $date->format('j');

            array_push($headers, [
                'key' => 'day_'.$singleDay,
                'label' => $singleDay,
                'print_label' => 'attendances._'.$singleDay.'.code',
                'visibility' => true,
            ]);

            $date->addDay(1);
        }

        return $headers;
    }

    public function paginate(Request $request): AnonymousResourceCollection
    {
        $request->validate(['date' => 'required|date']);

        $request->merge(['month_wise' => true]);

        $employees = (new FetchEmployee)->execute($request);

        $attendanceTypes = AttendanceType::query()
            ->byTeam()
            ->direct()
            ->get();

        $attendanceTypeSummaries = $this->getAttendanceTypeSummary($attendanceTypes);

        $request->merge(['attendance_types' => $attendanceTypeSummaries]);

        if ($request->boolean('day_wise')) {
            $employees = $this->getDayWiseRecords($request, $employees, $attendanceTypes);
        } else {
            $employees = $this->getSummaryRecords($request, $employees, $attendanceTypes);
        }

        return AttendanceResource::collection($employees)
            ->additional([
                'headers' => $this->getHeaders($request),
            ]);
    }

    private function getAttendanceTypeSummary(Collection $attendanceTypes): Collection
    {
        $attendanceTypeSummaries = $attendanceTypes->map(function ($attendanceType) {
            return [
                'code' => $attendanceType->code,
                'category' => $attendanceType->category,
                'name' => $attendanceType->name,
            ];
        });

        $attendanceTypeSummaries->push([
            'code' => 'L',
            'category' => 'leave',
            'name' => trans('employee.leave.leave'),
        ]);

        $attendanceTypeSummaries->push([
            'code' => 'LWP',
            'category' => 'leave',
            'name' => trans('employee.leave.leave_without_pay_short'),
        ]);

        return $attendanceTypeSummaries;
    }

    private function getDayWiseRecords(Request $request, LengthAwarePaginator $employees, Collection $attendanceTypes): LengthAwarePaginator
    {
        $attendances = Attendance::query()
            ->select('employee_id', 'remarks', 'date', 'attendance_symbol', 'attendance_types.name', 'attendance_types.category', 'attendance_types.code')
            ->leftJoin('attendance_types', function ($join) {
                $join->on('employee_attendances.attendance_type_id', '=', 'attendance_types.id');
            })
            ->whereIn('employee_id', $employees->pluck('id')->all())
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get();

        foreach ($employees as $employee) {
            $employeeAttendances = $attendances->where('employee_id', $employee->id);

            $date = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            $items = [];
            while ($date <= $endDate) {
                $day = $date->format('d');
                $singleDay = $date->format('j');

                $employeeAttendance = $employeeAttendances->firstWhere('date.value', $date->toDateString());

                $items['_'.$singleDay] = $this->getAttendance($employeeAttendance);

                $date->addDay(1);
            }

            $employee->list_attendance = true;
            $employee->attendances = $items;
        }

        return $employees;
    }

    private function getAttendance(?Attendance $employeeAttendance)
    {
        if (! $employeeAttendance) {
            return [
                'code' => '',
                'label' => '',
                'color' => AttendanceCategory::getColor(''),
            ];
        }

        if ($employeeAttendance->attendance_symbol == 'LWP') {
            return [
                'code' => 'LWP',
                'label' => trans('employee.leave.leave_without_pay_short'),
                'color' => 'warning',
            ];
        }

        if ($employeeAttendance->attendance_symbol == 'L') {
            return [
                'code' => 'L',
                'label' => trans('employee.leave.leave'),
                'color' => 'danger',
            ];
        }

        return [
            'code' => $employeeAttendance->code,
            'label' => $employeeAttendance->name,
            'color' => AttendanceCategory::getColor($employeeAttendance->category),
        ];
    }

    private function getSummaryRecords(Request $request, LengthAwarePaginator $employees, Collection $attendanceTypes): LengthAwarePaginator
    {
        $query = Attendance::query()
            ->select('employee_id')
            ->whereIn('employee_id', $employees->pluck('id')->all())
            ->whereBetween('date', [$request->start_date, $request->end_date]);

        foreach ($attendanceTypes as $attendanceType) {
            $query->selectRaw('count(case when attendance_type_id = '.$attendanceType->id.' then 1 end) as '.$attendanceType->code);
        }

        $attendances = $query
            ->selectRaw("count(case when attendance_symbol = 'L' then 1 end) as L")
            ->selectRaw("count(case when attendance_symbol = 'LWP' then 1 end) as LWP")
            ->selectRaw("count(case when JSON_EXTRACT(meta, '$.is_late') = true then 1 end) as late")
            ->selectRaw("sum(case when JSON_EXTRACT(meta, '$.is_late') = true then JSON_EXTRACT(meta, '$.late_duration') end) as total_late_duration")
            ->selectRaw("count(case when JSON_EXTRACT(meta, '$.is_early_leaving') = true then 1 end) as early_leaving")
            ->selectRaw("sum(case when JSON_EXTRACT(meta, '$.is_early_leaving') = true then JSON_EXTRACT(meta, '$.early_leaving_duration') end) as total_early_leaving_duration")
            ->selectRaw("count(case when JSON_EXTRACT(meta, '$.is_overtime') = true then 1 end) as overtime")
            ->selectRaw("sum(case when JSON_EXTRACT(meta, '$.is_overtime') = true then JSON_EXTRACT(meta, '$.overtime_duration') end) as total_overtime_duration")
            ->groupBy('employee_id')
            ->get();

        foreach ($employees as $employee) {
            $employeeSummary = $attendances->firstWhere('employee_id', $employee->id);

            $summary = [];
            $additionalSummary = [];

            foreach ($attendanceTypes as $attendanceType) {
                $code = $attendanceType->code;
                $summary['_'.$code] = $employeeSummary?->$code ?? 0;
            }
            $summary['_L'] = $employeeSummary?->L ?? 0;
            $summary['_LWP'] = $employeeSummary?->LWP ?? 0;

            $summary['_late'] = $employeeSummary?->late ?? 0;
            $summary['_early_leaving'] = $employeeSummary?->early_leaving ?? 0;
            $summary['_overtime'] = $employeeSummary?->overtime ?? 0;

            $additionalSummary['total_late_duration'] = $employeeSummary?->total_late_duration ?? 0;
            $additionalSummary['total_early_leaving_duration'] = $employeeSummary?->total_early_leaving_duration ?? 0;
            $additionalSummary['total_overtime_duration'] = $employeeSummary?->total_overtime_duration ?? 0;

            $employee->list_summary = true;
            $employee->summary = $summary;
            $employee->additional_summary = $additionalSummary;
        }

        return $employees;
    }

    public function list(Request $request): AnonymousResourceCollection
    {
        return $this->paginate($request);
    }
}
