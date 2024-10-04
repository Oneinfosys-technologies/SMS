<?php

namespace App\Services\Employee\Attendance;

use App\Actions\Employee\Attendance\StoreTimesheet;
use App\Models\Device;
use App\Models\Employee\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DeviceTimesheetService
{
    public function store(Request $request)
    {
        if (! config('config.employee.allow_employee_clock_in_out_via_device')) {
            throw ValidationException::withMessages(['message' => 'Attendance cannot be marked via device.', 'code' => 100]);
        }

        $device = Device::where('token', $request->token)->first();

        if (! $device) {
            throw ValidationException::withMessages(['message' => 'Invalid token.', 'code' => 101]);
        }

        $employee = Employee::query()
            ->whereCodeNumber($request->employee_code)
            ->where('joining_date', '<=', today()->toDateString())
            ->where(function ($q) {
                $q->whereNull('leaving_date')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('leaving_date')
                            ->where('leaving_date', '>=', today()->toDateString());
                    });
            })
            ->first();

        if (! $employee) {
            throw ValidationException::withMessages(['message' => 'Invalid Employee code.', 'code' => 102]);
        }

        $dateTime = $request->date_time ?: now()->timezone(config('config.system.timezone'))->toDateTimeString();

        (new StoreTimesheet)->execute($employee, $dateTime);

        return ['message' => 'Attendance marked successfully.', 'code' => 200];
    }
}
