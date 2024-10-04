<?php

namespace App\Http\Controllers\Employee\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\Leave\RequestStatusRequest as LeaveRequestStatusRequest;
use App\Services\Employee\Leave\RequestActionService as LeaveRequestActionService;

class RequestActionController extends Controller
{
    public function updateStatus(LeaveRequestStatusRequest $request, string $leaveRequest, LeaveRequestActionService $service)
    {
        $leaveRequest = $request->leave_request;

        $this->authorize('action', $leaveRequest);

        $service->updateStatus($request, $leaveRequest);

        return response()->success([
            'message' => trans('global.updated', ['attribute' => trans('employee.leave.request.props.status')]),
        ]);
    }
}
