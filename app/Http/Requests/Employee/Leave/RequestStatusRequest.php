<?php

namespace App\Http\Requests\Employee\Leave;

use App\Enums\Employee\Leave\RequestStatus as LeaveRequestStatus;
use App\Helpers\CalHelper;
use App\Models\Employee\Leave\Allocation as LeaveAllocation;
use App\Models\Employee\Leave\Request as LeaveRequest;
use App\Models\Employee\Payroll\Payroll;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class RequestStatusRequest extends FormRequest
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
            'status' => ['required', new Enum(LeaveRequestStatus::class)],
            'comment' => 'required|min:10|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {
            $uuid = $this->route('leave_request');

            $leaveRequest = LeaveRequest::query()
                ->whereUuid($uuid)
                ->with(['model' => fn ($q) => $q->summary()])
                ->getOrFail(trans('employee.leave.request.request'), 'message');

            $payrollGenerated = Payroll::query()
                ->whereEmployeeId($leaveRequest->employee_id)
                ->betweenPeriod($leaveRequest->start_date->value, $leaveRequest->end_date->value)
                ->exists();

            if ($payrollGenerated) {
                throw ValidationException::withMessages(['message' => trans('employee.leave.request.could_not_perform_if_payroll_generated')]);
            }

            $duration = CalHelper::dateDiff($leaveRequest->start_date->value, $leaveRequest->end_date->value);

            $requestWithExhaustedCredit = config('config.employee.allow_employee_request_leave_with_exhausted_credit');

            $query = LeaveAllocation::query()
                ->with('records')
                ->whereEmployeeId($leaveRequest->model_id)
                ->where('start_date', '<=', $leaveRequest->start_date->value)
                ->where('end_date', '>=', $leaveRequest->end_date->value);

            if ($requestWithExhaustedCredit) {
                $leaveAllocation = $query->first();
            } else {
                $leaveAllocation = $query->getOrFail(trans('employee.leave.allocation.allocation'));
            }

            if ($leaveAllocation) {
                if ($requestWithExhaustedCredit) {
                    $leaveAllocationRecord = $leaveAllocation->records->where('leave_type_id', $leaveRequest->leave_type_id)->first();
                } else {
                    $leaveAllocationRecord = $leaveAllocation->records->where('leave_type_id', $leaveRequest->leave_type_id)->hasOrFail(trans('employee.leave.type.no_allocation_found'));
                }
            } else {
                $leaveAllocationRecord = null;
            }

            $balance = 0;

            if ($leaveAllocationRecord) {
                $balance = $leaveAllocationRecord->allotted - $leaveAllocationRecord->used;
            }

            if (! $requestWithExhaustedCredit) {
                if ($this->status == LeaveRequestStatus::APPROVED->value) {
                    if ($leaveRequest->status != LeaveRequestStatus::APPROVED && $balance < $duration) {
                        throw ValidationException::withMessages(['message' => trans('employee.leave.type.balance_exhausted', ['balance' => $balance, 'duration' => $duration])]);
                    }
                }
            }

            $this->merge([
                'leave_request' => $leaveRequest,
                'leave_allocation_id' => $leaveAllocation?->id,
                'duration' => $duration,
                'balance' => $balance,
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
            'status' => __('employee.leave.request.props.status'),
            'comment' => __('employee.leave.request.props.comment'),
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
