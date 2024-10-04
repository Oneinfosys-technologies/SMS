<?php

namespace App\Actions\Config\Module;

class StoreStudentConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
            'registration_number_prefix' => 'sometimes|max:200',
            'registration_number_digit' => 'sometimes|required|integer|min:0|max:9',
            'registration_number_suffix' => 'sometimes|max:200',
            'admission_number_prefix' => 'sometimes|max:200',
            'admission_number_digit' => 'sometimes|required|integer|min:0|max:9',
            'admission_number_suffix' => 'sometimes|max:200',
            'transfer_request_number_prefix' => 'sometimes|max:200',
            'transfer_request_number_digit' => 'sometimes|required|integer|min:0|max:9',
            'transfer_request_number_suffix' => 'sometimes|max:200',
            'transfer_number_prefix' => 'sometimes|max:200',
            'transfer_number_digit' => 'sometimes|required|integer|min:0|max:9',
            'transfer_number_suffix' => 'sometimes|max:200',
            'attendance_past_day_limit' => 'sometimes|required|integer|min:0',
            'allow_student_to_submit_contact_edit_request' => 'sometimes|boolean',
            'late_fee_waiver_till_date' => 'sometimes|date_format:Y-m-d',
        ], [], [
            'registration_number_prefix' => __('student.registration.config.props.number_prefix'),
            'registration_number_digit' => __('student.registration.config.props.number_digit'),
            'registration_number_suffix' => __('student.registration.config.props.number_suffix'),
            'admission_number_prefix' => __('student.config.props.number_prefix'),
            'admission_number_digit' => __('student.config.props.number_digit'),
            'admission_number_suffix' => __('student.config.props.number_suffix'),
            'transfer_request_number_prefix' => __('student.transfer_request.config.props.number_prefix'),
            'transfer_request_number_digit' => __('student.transfer_request.config.props.number_digit'),
            'transfer_request_number_suffix' => __('student.transfer_request.config.props.number_suffix'),
            'transfer_number_prefix' => __('student.transfer.config.props.number_prefix'),
            'transfer_number_digit' => __('student.transfer.config.props.number_digit'),
            'transfer_number_suffix' => __('student.transfer.config.props.number_suffix'),
            'attendance_past_day_limit' => __('student.config.props.attendance_past_day_limit'),
            'allow_student_to_submit_contact_edit_request' => __('student.config.props.allow_student_to_submit_contact_edit_request'),
            'late_fee_waiver_till_date' => __('student.config.props.late_fee_waiver_till_date'),
        ]);

        return $input;
    }
}
