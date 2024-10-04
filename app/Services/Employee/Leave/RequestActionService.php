<?php

namespace App\Services\Employee\Leave;

use App\Enums\Employee\Leave\RequestStatus as LeaveRequestStatus;
use App\Helpers\CalHelper;
use App\Models\Employee\Attendance\Attendance;
use App\Models\Employee\Leave\AllocationRecord as LeaveAllocationRecord;
use App\Models\Employee\Leave\Request as LeaveRequest;
use App\Models\Employee\Leave\RequestRecord as LeaveRequestRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestActionService
{
    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        \DB::beginTransaction();

        $this->updateLeaveBalance($request, $leaveRequest, 'decrement');

        $leaveRequestRecord = LeaveRequestRecord::firstOrCreate([
            'leave_request_id' => $leaveRequest->id,
        ]);

        $leaveRequestRecord->approve_user_id = auth()->id();
        $leaveRequestRecord->status = $request->status;
        $leaveRequestRecord->comment = $request->comment;
        $leaveRequestRecord->save();

        $leaveRequest->status = $request->status;

        if ($request->balance == 0 && config('config.employee.allow_employee_request_leave_with_exhausted_credit')) {
            $leaveRequest->setMeta(['leave_with_exhausted_credit' => true]);
        }

        $leaveRequest->save();

        $this->updateLeaveBalance($request, $leaveRequest);

        $this->updateAttendance($leaveRequest);

        \DB::commit();
    }

    private function updateLeaveBalance(Request $request, LeaveRequest $leaveRequest, $action = 'increment'): void
    {
        if ($leaveRequest->getMeta('leave_with_exhausted_credit')) {
            return;
        }

        if (! in_array($leaveRequest->status, [LeaveRequestStatus::APPROVED])) {
            return;
        }

        LeaveAllocationRecord::query()
            ->whereLeaveAllocationId($request->leave_allocation_id)
            ->whereLeaveTypeId($leaveRequest->leave_type_id)
            ->$action('used', $request->duration);
    }

    private function updateAttendance(LeaveRequest $leaveRequest): void
    {
        $attendanceCode = $leaveRequest->getMeta('leave_with_exhausted_credit') ? 'LWP' : 'L';

        $dates = CalHelper::datesInPeriod($leaveRequest->start_date->value, $leaveRequest->end_date->value);

        if ($leaveRequest->status != LeaveRequestStatus::APPROVED) {
            Attendance::whereIn('date', $dates)->whereEmployeeId($leaveRequest->model_id)->whereAttendanceSymbol($attendanceCode)->delete();

            return;
        }

        Attendance::whereIn('date', $dates)->whereEmployeeId($leaveRequest->model_id)->whereNull('attendance_symbol')->delete();

        $attendances = [];
        foreach ($dates as $date) {
            $attendances[] = ['date' => $date, 'employee_id' => $leaveRequest->model_id, 'attendance_symbol' => $attendanceCode, 'attendance_type_id' => null, 'uuid' => (string) Str::uuid()];
        }

        Attendance::upsert(
            $attendances,
            ['date', 'employee_id'],
            ['attendance_symbol', 'attendance_type_id', 'uuid']
        );
    }
}
