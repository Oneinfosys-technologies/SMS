<?php

namespace App\Actions\Config;

use App\Helpers\ListHelper;

class StoreSMSConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
            'driver' => 'required|in:'.implode(',', ListHelper::getListKey('sms_drivers')),
            'sender_id' => 'sometimes|required',
            'test_number' => 'sometimes|required',
            'api_key' => 'sometimes|required_if:driver,twilio',
            'api_secret' => 'sometimes|required_if:driver,twilio',
            'api_url' => 'sometimes|required_if:driver,custom',
            'number_prefix' => 'sometimes|nullable',
            'sender_id_param' => 'sometimes|required_if:driver,custom',
            'receiver_param' => 'sometimes|required_if:driver,custom',
            'message_param' => 'sometimes|required_if:driver,custom',
        ], [
            'api_url.required_if' => __('validation.required', ['attribute' => __('config.sms.props.api_url')]),
            'number_prefix.required_if' => __('validation.required', ['attribute' => __('config.sms.props.number_prefix')]),
            'sender_id_param.required_if' => __('validation.required', ['attribute' => __('config.sms.props.sender_id_param')]),
            'receiver_param.required_if' => __('validation.required', ['attribute' => __('config.sms.props.receiver_param')]),
            'message_param.required_if' => __('validation.required', ['attribute' => __('config.sms.props.message_param')]),
        ], [
            'driver' => __('config.sms.props.driver'),
            'sender_id' => __('config.sms.props.sender_id'),
            'test_number' => __('config.sms.props.test_number'),
            'api_key' => __('config.sms.props.api_key'),
            'api_secret' => __('config.sms.props.api_secret'),
            'api_url' => __('config.sms.props.api_url'),
            'number_prefix' => __('config.sms.props.number_prefix'),
            'sender_id_param' => __('config.sms.props.sender_id_param'),
            'receiver_param' => __('config.sms.props.receiver_param'),
            'message_param' => __('config.sms.props.message_param'),
        ]);

        return $input;
    }
}
