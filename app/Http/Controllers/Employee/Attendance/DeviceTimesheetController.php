<?php

namespace App\Http\Controllers\Employee\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\Attendance\DeviceTimesheetRequest;
use App\Services\Employee\Attendance\DeviceTimesheetService;

class DeviceTimesheetController extends Controller
{
    public function store(DeviceTimesheetRequest $request, DeviceTimesheetService $service)
    {
        $response = $service->store($request);

        return response()->success($response);
    }
}
