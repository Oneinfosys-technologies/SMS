<?php

namespace App\Http\Resources\Employee\Payroll;

use App\Enums\Employee\Payroll\PayHeadCategory;
use App\Enums\Finance\PaymentStatus;
use App\Http\Resources\Employee\EmployeeSummaryResource;
use App\Models\Employee\Attendance\Type as AttendanceType;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class PayrollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'code_number' => $this->code_number,
            'salary_structure' => SalaryStructureResource::make($this->whenLoaded('salaryStructure')),
            'employee' => EmployeeSummaryResource::make($this->whenLoaded('employee')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'period' => $this->period,
            'duration' => $this->duration,
            'total' => $this->total,
            'paid' => $this->paid,
            'is_paid' => $this->total->value > $this->paid->value ? false : true,
            'has_hourly_payroll' => (bool) $this->getMeta('has_hourly_payroll'),
            'status' => PaymentStatus::getDetail($this->status),
            'remarks' => $this->remarks,
            'net_earning' => \Price::from($this->getMeta('actual.earning')),
            'net_deduction' => \Price::from($this->getMeta('actual.deduction')),
            'employee_contribution' => \Price::from($this->getMeta('actual.employee_contribution')),
            'employer_contribution' => \Price::from($this->getMeta('actual.employer_contribution')),
            'net_salary' => \Price::from($this->getMeta('actual.earning') - $this->getMeta('actual.deduction') - $this->getMeta('actual.employee_contribution')),
            $this->mergeWhen($request->show_attendance_summary, [
                'attendance_summary' => $this->getAttendanceSummary(),
            ]),
            $this->mergeWhen($this->getMeta('has_hourly_payroll'), [
                'records' => [
                    [
                        'amount' => \Price::from($this->getMeta('actual.earning')),
                        'pay_head' => [
                            'name' => trans('payroll.salary_structure.props.hourly_pay'),
                            'category' => PayHeadCategory::getDetail('earning'),
                            'code' => 'WHP',
                        ],
                    ],
                ],
            ]),
            $this->mergeWhen(! $this->getMeta('has_hourly_payroll'), [
                'records' => RecordResource::collection($this->whenLoaded('records')),
            ]),
            'created_at' => \Cal::dateTime($this->created_at),
            'updated_at' => \Cal::dateTime($this->updated_at),
        ];
    }

    private function getAttendanceSummary()
    {
        $attendanceTypes = AttendanceType::byTeam()->get();

        $attendances = [];
        $payrollAttendances = $this->getMeta('attendances') ?? [];
        foreach ($payrollAttendances as $attendance) {
            if (Arr::get($attendance, 'code') == 'L') {
                $attendances[] = Arr::add($attendance, 'name', trans('employee.leave.leave'));
            } elseif (Arr::get($attendance, 'code') == 'WH') {
                $attendances[] = Arr::add($attendance, 'name', trans('employee.payroll.salary_structure.working_hours'));
            }

            $attendanceType = $attendanceTypes->firstWhere('code', Arr::get($attendance, 'code'));

            if ($attendanceType) {
                $attendances[] = Arr::add($attendance, 'name', $attendanceType->name);
            }
        }

        return $attendances;
    }
}
