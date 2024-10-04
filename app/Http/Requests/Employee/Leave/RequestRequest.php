<?php

namespace App\Http\Requests\Employee\Leave;

use App\Helpers\CalHelper;
use App\Models\Employee\Employee;
use App\Models\Employee\Leave\Allocation as LeaveAllocation;
use App\Models\Employee\Leave\Request as LeaveRequest;
use App\Models\Employee\Leave\Type as LeaveType;
use App\Models\Employee\Payroll\Payroll;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RequestRequest extends FormRequest
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
        return [
            'leave_type' => 'required|uuid',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|min:10|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $uuid = $this->route('leave_request');

            $employee = Employee::auth()->first();

            if (! $employee) {
                throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
            }

            $leaveType = LeaveType::query()
                ->byTeam()
                ->whereUuid($this->leave_type)
                ->getOrFail(trans('employee.leave.type.type'), 'leave_type');

            $overlappingRequest = LeaveRequest::query()
                ->whereModelType('Employee')
                ->whereModelId($employee->id)
                ->when($uuid, function ($q, $uuid) {
                    $q->where('uuid', '!=', $uuid);
                })
                ->betweenPeriod($this->start_date, $this->end_date)
                ->count();

            if ($overlappingRequest) {
                $validator->errors()->add('message', trans('employee.leave.type.range_exists', ['start' => CalHelper::showDate($this->start_date), 'end' => CalHelper::showDate($this->end_date)]));
            }

            $duration = CalHelper::dateDiff($this->start_date, $this->end_date);

            $payrollGenerated = Payroll::query()
                ->whereEmployeeId($employee->id)
                ->betweenPeriod($this->start_date, $this->end_date)
                ->exists();

            if ($payrollGenerated) {
                throw ValidationException::withMessages(['message' => trans('employee.leave.request.could_not_perform_if_payroll_generated')]);
            }

            $requestWithExhaustedCredit = config('config.employee.allow_employee_request_leave_with_exhausted_credit');

            $query = LeaveAllocation::query()
                ->with('records')
                ->whereEmployeeId($employee->id)
                ->where('start_date', '<=', $this->start_date)
                ->where('end_date', '>=', $this->end_date);

            if ($requestWithExhaustedCredit) {
                $leaveAllocation = $query->first();
            } else {
                $leaveAllocation = $query->getOrFail(trans('employee.leave.allocation.allocation'));
            }

            if (! $requestWithExhaustedCredit) {
                $leaveAllocationRecord = $leaveAllocation->records->where('leave_type_id', $leaveType->id)->hasOrFail(trans('employee.leave.type.no_allocation_found'));

                $balance = $leaveAllocationRecord->allotted - $leaveAllocationRecord->used;

                if ($balance < $duration) {
                    throw ValidationException::withMessages(['message' => trans('employee.leave.type.balance_exhausted', ['balance' => $balance, 'duration' => $duration])]);
                }
            }

            $this->merge([
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'duration' => $duration,
                'leave_allocation_id' => $leaveAllocation?->id,
            ]);
        });
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'leave_type' => __('employee.leave.type.type'),
            'start_date' => __('employee.leave.request.props.start_date'),
            'end_date' => __('employee.leave.request.props.end_date'),
            'reason' => __('employee.leave.request.props.reason'),
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
