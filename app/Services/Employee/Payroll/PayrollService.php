<?php

namespace App\Services\Employee\Payroll;

use App\Actions\Employee\Payroll\CalculatePayroll;
use App\Enums\Finance\PaymentStatus;
use App\Models\Employee\Payroll\Payroll;
use App\Models\Employee\Payroll\Record;
use App\Support\Evaluator;
use App\Support\FormatCodeNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class PayrollService
{
    use Evaluator, FormatCodeNumber;

    private function codeNumber(Request $request)
    {
        $numberPrefix = config('config.employee.payroll_number_prefix');
        $numberSuffix = config('config.employee.payroll_number_suffix');
        $digit = config('config.employee.payroll_number_digit', 0);

        $numberFormat = $numberPrefix.'%NUMBER%'.$numberSuffix;
        $codeNumber = (int) Payroll::whereHas('employee', function ($q) {
            $q->byTeam();
        })->whereNumberFormat($numberFormat)->max('number') + 1;

        return $this->getCodeNumber(number: $codeNumber, digit: $digit, format: $numberFormat);
    }

    private function validateCodeNumber(Request $request, ?string $uuid = null): array
    {
        if (! $request->code_number) {
            return $this->codeNumber($request);
        }

        $duplicateCodeNumber = Payroll::whereHas('employee', function ($q) {
            $q->byTeam();
        })->whereCodeNumber($request->code_number)->when($uuid, function ($q, $uuid) {
            $q->where('uuid', '!=', $uuid);
        })->count();

        if ($duplicateCodeNumber) {
            throw ValidationException::withMessages(['message' => trans('global.duplicate', ['attribute' => trans('employee.payroll.config.props.code_number')])]);
        }
    }

    public function preRequisite(Request $request): array
    {
        return [];
    }

    public function fetch(Request $request): array
    {
        [$attendanceSummary, $data] = (new CalculatePayroll)->execute(
            employeeId: $request->employee->id,
            startDate: $request->start_date,
            endDate: $request->end_date,
            salaryStructure: $request->salary_structure,
            attendanceTypes: $request->attendance_types,
            productionAttendanceTypes: $request->production_attendance_types,
        );

        $netEarning = Arr::get($data, 'earning_component');
        $netDeduction = Arr::get($data, 'deduction_component');
        $employeeContribution = Arr::get($data, 'employee_contribution');
        $employerContribution = Arr::get($data, 'employer_contribution');
        $records = Arr::get($data, 'pay_heads');

        $records = collect($records)->filter(function ($record) {
            return $record['category'] != 'component' && $record['category'] != 'gross';
        })->map(function ($record) {
            return Arr::except($record, ['id']);
        })->map(function ($record) {
            return [
                'amount' => $record['amount'],
                'pay_head' => [
                    ...Arr::except($record, ['amount']),
                ],
            ];
        })->values()->all();

        $netSalary = $netEarning - $netDeduction - $employeeContribution;

        return compact('attendanceSummary', 'records', 'netEarning', 'netDeduction', 'netSalary', 'employeeContribution', 'employerContribution');
    }

    public function create(Request $request): Payroll
    {
        \DB::beginTransaction();

        $payroll = Payroll::forceCreate($this->formatParams($request));

        $salaryStructure = $request->salary_structure;

        if (! $salaryStructure->template->has_hourly_payroll) {
            $this->updateRecords($request, $payroll);
        }

        \DB::commit();

        return $payroll;
    }

    private function formatParams(Request $request, ?Payroll $payroll = null): array
    {
        $codeNumberDetail = $this->validateCodeNumber($request, $payroll?->uuid);

        $formatted = [
            'remarks' => $request->remarks,
        ];

        if (! $payroll) {
            $formatted['employee_id'] = $request->employee->id;
            $formatted['salary_structure_id'] = $request->salary_structure->id;
            $formatted['start_date'] = $request->start_date;
            $formatted['end_date'] = $request->end_date;
            $formatted['number_format'] = Arr::get($codeNumberDetail, 'number_format');
            $formatted['number'] = Arr::get($codeNumberDetail, 'number');
            $formatted['code_number'] = Arr::get($codeNumberDetail, 'code_number', $request->code_number);
        }

        return $formatted;
    }

    private function updateRecords(Request $request, Payroll $payroll): void
    {
        $data = $this->fetch($request);

        $calculatedRecords = Arr::get($data, 'records', []);

        $calculatedAttendances = Arr::get($data, 'attendanceSummary', []);
        $attendances = Arr::map($calculatedAttendances, function ($item) {
            return ['code' => Arr::get($item, 'code'), 'count' => Arr::get($item, 'count'), 'unit' => Arr::get($item, 'unit')];
        });

        $earningComponent = Arr::get($data, 'netEarning');
        $deductionComponent = Arr::get($data, 'netDeduction');
        $employeeContribution = Arr::get($data, 'employeeContribution');
        $employerContribution = Arr::get($data, 'employerContribution');
        $netSalary = $earningComponent - $deductionComponent - $employeeContribution;

        $calculated = [
            'earning' => $earningComponent,
            'deduction' => $deductionComponent,
            'employee_contribution' => $employeeContribution,
            'employer_contribution' => $employerContribution,
            'salary' => $netSalary,
        ];

        $actual = [
            'earning' => $request->earning,
            'deduction' => $request->deduction,
            'employee_contribution' => $request->employee_contribution,
            'employer_contribution' => $request->employer_contribution,
            'salary' => $request->total,
        ];

        $meta = $payroll->meta;
        $meta['attendances'] = $attendances;
        $meta['calculated'] = $calculated;
        $meta['actual'] = $actual;

        if ($request->salary_structure->template->has_hourly_payroll) {
            $meta['has_hourly_payroll'] = true;
        }

        $payroll->meta = $meta;

        $payroll->total = $request->total;
        $payroll->status = PaymentStatus::UNPAID->value;
        $payroll->save();

        if ($request->salary_structure->template->has_hourly_payroll) {
            $payroll->records()->delete();

            return;
        }

        foreach ($request->records as $record) {
            $calculatedRecord = Arr::first($calculatedRecords, function ($item) use ($record) {
                return Arr::get($item, 'pay_head.uuid') == Arr::get($record, 'pay_head.uuid');
            });

            $payrollRecord = Record::firstOrCreate([
                'payroll_id' => $payroll->id,
                'pay_head_id' => Arr::get($record, 'pay_head.id'),
            ]);

            $payrollRecord->calculated = Arr::get($calculatedRecord, 'amount');
            $payrollRecord->amount = Arr::get($record, 'amount');

            $payrollRecord->save();
        }
    }

    private function ensureIsLastPayroll(Payroll $payroll): void
    {
        $isLastPayroll = Payroll::whereEmployeeId($payroll->employee_id)->where('start_date', '>', $payroll->start_date->value)->doesntExist();

        if (! $isLastPayroll) {
            throw ValidationException::withMessages(['message' => trans('global.could_not_modify_past_record', ['attribute' => trans('employee.payroll.payroll')])]);
        }
    }

    public function update(Request $request, Payroll $payroll): void
    {
        $this->ensureIsLastPayroll($payroll);

        \DB::beginTransaction();

        $payroll->forceFill($this->formatParams($request, $payroll))->save();

        $this->updateRecords($request, $payroll);

        \DB::commit();
    }

    public function deletable(Payroll $payroll): void
    {
        $this->ensureIsLastPayroll($payroll);
    }
}
