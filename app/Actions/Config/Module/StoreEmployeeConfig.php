<?php

namespace App\Actions\Config\Module;

use App\Rules\Latitude;
use App\Rules\Longitude;

class StoreEmployeeConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
            'code_number_prefix' => 'sometimes|max:100',
            'code_number_digit' => 'sometimes|required|integer|min:0|max:9',
            'code_number_suffix' => 'sometimes|max:100',
            'unique_id_number1_label' => 'sometimes|required|min:2|max:100',
            'unique_id_number2_label' => 'sometimes|required|min:2|max:100',
            'unique_id_number3_label' => 'sometimes|required|min:2|max:100',
            'is_unique_id_number1_required' => 'sometimes|boolean',
            'is_unique_id_number2_required' => 'sometimes|boolean',
            'is_unique_id_number3_required' => 'sometimes|boolean',
            'allow_employee_request_leave_with_exhausted_credit' => 'sometimes|boolean',
            'allow_employee_clock_in_out' => 'sometimes|boolean',
            'allow_employee_clock_in_out_via_device' => 'sometimes|boolean',
            'late_grace_period' => 'sometimes|numeric|min:0|max:60',
            'early_leaving_grace_period' => 'sometimes|numeric|min:0|max:60',
            'present_grace_period' => 'sometimes|numeric|min:0|max:120',
            'enable_geolocation_timesheet' => 'sometimes|boolean',
            'geolocation_latitude' => ['sometimes', 'required_if:enable_geolocation_timesheet,1', new Latitude],
            'geolocation_longitude' => ['sometimes', 'required_if:enable_geolocation_timesheet,1', new Longitude],
            'geolocation_radius' => 'sometimes|required_if:enable_geolocation_timesheet,1|numeric',
        ], [
            'geolocation_latitude.required_if' => __('validation.required', ['attribute' => __('employee.attendance.config.props.geolocation_latitude')]),
            'geolocation_longitude.required_if' => __('validation.required', ['attribute' => __('employee.attendance.config.props.geolocation_longitude')]),
            'geolocation_radius.required_if' => __('validation.required', ['attribute' => __('employee.attendance.config.props.geolocation_radius')]),
        ], [
            'code_number_prefix' => __('employee.config.props.number_prefix'),
            'code_number_digit' => __('employee.config.props.number_digit'),
            'code_number_suffix' => __('employee.config.props.number_suffix'),
            'unique_id_number1_label' => __('employee.config.props.unique_id_number1_label'),
            'unique_id_number2_label' => __('employee.config.props.unique_id_number2_label'),
            'unique_id_number3_label' => __('employee.config.props.unique_id_number3_label'),
            'is_unique_id_number1_required' => __('employee.config.props.unique_id_number1_required'),
            'is_unique_id_number2_required' => __('employee.config.props.unique_id_number2_required'),
            'is_unique_id_number3_required' => __('employee.config.props.unique_id_number3_required'),
            'allow_employee_request_leave_with_exhausted_credit' => __('employee.leave.config.props.allow_employee_request_leave_with_exhausted_credit'),
            'allow_employee_clock_in_out' => __('employee.attendance.config.props.allow_employee_clock_in_out'),
            'allow_employee_clock_in_out_via_device' => __('employee.attendance.config.props.allow_employee_clock_in_out_via_device'),
            'late_grace_period' => __('employee.attendance.config.props.late_grace_period'),
            'early_leaving_grace_period' => __('employee.attendance.config.props.early_leaving_grace_period'),
            'present_grace_period' => __('employee.attendance.config.props.present_grace_period'),
            'enable_geolocation_timesheet' => __('employee.attendance.config.props.enable_geolocation_timesheet'),
            'geolocation_latitude' => __('employee.attendance.config.props.geolocation_latitude'),
            'geolocation_longitude' => __('employee.attendance.config.props.geolocation_longitude'),
            'geolocation_radius' => __('employee.attendance.config.props.geolocation_radius'),
        ]);

        return $input;
    }
}
