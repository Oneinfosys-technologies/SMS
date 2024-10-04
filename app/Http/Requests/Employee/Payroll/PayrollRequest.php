<?php

namespace App\Http\Requests\Employee\Payroll;

use App\Enums\Employee\Payroll\PayHeadCategory;
use App\Helpers\CalHelper;
use App\Helpers\SysHelper;
use App\Models\Employee\Attendance\Type as AttendanceType;
use App\Models\Employee\Employee;
use App\Models\Employee\Payroll\Payroll;
use App\Models\Employee\Payroll\SalaryStructure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class PayrollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'employee' => 'required|uuid',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date',
        ];

        if ($this->has('records')) {
            $rules['records'] = 'required|array|min:1';
            $rules['records.*.amount'] = 'required|numeric|min:0';
            $rules['remarks'] = 'nullable|min:2|max:1000';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $uuid = $this->route('payroll');

            $employee = Employee::query()
                ->basic()
                ->filterAccessible()
                ->where('employees.uuid', $this->employee)
                ->getOrFail(trans('employee.employee'), 'employee');

            $payroll = Payroll::query()
                ->when($uuid, function ($q, $uuid) {
                    $q->where('uuid', '!=', $uuid);
                })
                ->whereEmployeeId($employee->id)
                ->where('start_date', '>', $this->start_date)
                ->exists();

            if ($payroll) {
                $validator->errors()->add('start_date', trans('employee.payroll.could_not_perform_if_payroll_generated_for_later_date'));
            }

            $overlappingPayroll = Payroll::query()
                ->when($uuid, function ($q, $uuid) {
                    $q->where('uuid', '!=', $uuid);
                })
                ->whereEmployeeId($employee->id)
                ->betweenPeriod($this->start_date, $this->end_date)
                ->exists();

            if ($overlappingPayroll) {
                $validator->errors()->add('employee', trans('employee.payroll.range_exists', ['start' => CalHelper::showDate($this->start_date), 'end' => CalHelper::showDate($this->end_date)]));
            }

            $salaryStructure = SalaryStructure::query()
                ->whereEmployeeId($employee->id)
                ->where('effective_date', '<=', $this->start_date)
                ->orderBy('effective_date', 'desc')
                ->first();

            if (! $salaryStructure) {
                $validator->errors()->add('employee', trans('global.could_not_find', ['attribute' => trans('employee.payroll.salary_structure.salary_structure')]));

                return;
            } else {
                $salaryStructure->load('records', 'template', 'template.records', 'template.records.payHead');
            }

            $this->merge([
                'employee' => $employee,
                'salary_structure' => $salaryStructure,
                'attendance_types' => AttendanceType::byTeam()->direct()->get(),
                'production_attendance_types' => AttendanceType::byTeam()->productionBased()->get(),
            ]);

            if ($salaryStructure->template->has_hourly_payroll) {
                $this->getHourlyPayrollData();

                return;
            }

            if (! $this->has('records')) {
                return;
            }

            $salaryTemplateRecords = $salaryStructure->template->records;
            $payHeads = $salaryTemplateRecords->map(function ($record) {
                return $record->payHead;
            });
            $payHeadUuids = $payHeads->pluck('uuid')->all();

            $earning = 0;
            $deduction = 0;
            $employeeContribution = 0;
            $employerContribution = 0;
            $newRecords = [];
            foreach ($this->records as $index => $record) {
                $payHead = $payHeads->firstWhere('uuid', Arr::get($record, 'pay_head.uuid'));

                if (! $payHead) {
                    $validator->errors()->add('records.'.$index.'.amount', trans('global.could_not_find', ['attribute' => trans('employee.payroll.pay_head.pay_head')]));
                } else {
                    if ($payHead->category == PayHeadCategory::EARNING) {
                        $earning += SysHelper::formatAmount(Arr::get($record, 'amount', 0));
                    } elseif ($payHead->category == PayHeadCategory::DEDUCTION) {
                        $deduction += SysHelper::formatAmount(Arr::get($record, 'amount', 0));
                    } elseif ($payHead->category == PayHeadCategory::EMPLOYEE_CONTRIBUTION) {
                        $employeeContribution += SysHelper::formatAmount(Arr::get($record, 'amount', 0));
                    } elseif ($payHead->category == PayHeadCategory::EMPLOYER_CONTRIBUTION) {
                        $employerContribution += SysHelper::formatAmount(Arr::get($record, 'amount', 0));
                    }

                    $newRecords[] = Arr::add($record, 'pay_head.id', $payHead->id);
                }
            }

            $this->merge([
                'earning' => $earning,
                'deduction' => $deduction,
                'employee_contribution' => $employeeContribution,
                'employer_contribution' => $employerContribution,
                'total' => SysHelper::formatAmount($earning - $deduction - $employeeContribution),
                'records' => $newRecords,
            ]);
        });
    }

    private function getHourlyPayrollData()
    {
        $earning = 0;
        $deduction = 0;
        $employeeContribution = 0;
        $employerContribution = 0;
        foreach ($this->records ?? [] as $record) {
            if (Arr::get($record, 'pay_head.category') == PayHeadCategory::EARNING->value) {
                $earning += SysHelper::formatAmount(Arr::get($record, 'amount', 0));
            } elseif (Arr::get($record, 'pay_head.category') == PayHeadCategory::DEDUCTION->value) {
                $deduction += SysHelper::formatAmount(Arr::get($record, 'amount', 0));
            } elseif (Arr::get($record, 'pay_head.category') == PayHeadCategory::EMPLOYEE_CONTRIBUTION->value) {
                $employeeContribution += SysHelper::formatAmount(Arr::get($record, 'amount', 0));
            } elseif (Arr::get($record, 'pay_head.category') == PayHeadCategory::EMPLOYER_CONTRIBUTION->value) {
                $employerContribution += SysHelper::formatAmount(Arr::get($record, 'amount', 0));
            }
        }

        $this->merge([
            'earning' => $earning,
            'deduction' => $deduction,
            'total' => SysHelper::formatAmount($earning - $deduction),
        ]);
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'employee' => trans('employee.employee'),
            'start_date' => trans('general.period'),
            'end_date' => trans('general.period'),
            'records.*.amount' => trans('employee.payroll.props.amount'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
