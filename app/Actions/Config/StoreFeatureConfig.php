<?php

namespace App\Actions\Config;

class StoreFeatureConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
            'enable_todo' => 'sometimes|boolean',
            'enable_backup' => 'sometimes|boolean',
            'enable_activity_log' => 'sometimes|boolean',
            'enable_guest_payment' => 'sometimes|boolean',
            'guest_payment_instruction' => 'sometimes|max:2000',
            'enable_online_registration' => 'sometimes|boolean',
            'online_registration_instruction' => 'sometimes|max:2000',
            'enable_job_application' => 'sometimes|boolean',
            'job_application_instruction' => 'sometimes|max:2000',
        ], [], [
            'enable_todo' => __('config.feature.props.todo'),
            'enable_backup' => __('config.feature.props.backup'),
            'enable_activity_log' => __('config.feature.props.activity_log'),
            'enable_guest_payment' => __('config.feature.props.guest_payment'),
            'guest_payment_instruction' => __('config.feature.props.guest_payment_instruction'),
            'enable_online_registration' => __('config.feature.props.online_registration'),
            'online_registration_instruction' => __('config.feature.props.online_registration_instruction'),
            'enable_job_application' => __('config.feature.props.job_application'),
            'job_application_instruction' => __('config.feature.props.job_application_instruction'),
        ]);

        $input['guest_payment_instruction'] = clean($input['guest_payment_instruction']);
        $input['online_registration_instruction'] = clean($input['online_registration_instruction']);
        $input['job_application_instruction'] = clean($input['job_application_instruction']);

        return $input;
    }
}
