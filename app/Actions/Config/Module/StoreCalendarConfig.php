<?php

namespace App\Actions\Config\Module;

class StoreCalendarConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
            'event_number_prefix' => 'sometimes|max:200',
            'event_number_digit' => 'sometimes|required|integer|min:0|max:9',
            'event_number_suffix' => 'sometimes|max:200',
        ], [], [
            'event_number_prefix' => __('calendar.event.config.props.number_prefix'),
            'event_number_digit' => __('calendar.event.config.props.number_digit'),
            'event_number_suffix' => __('calendar.event.config.props.number_suffix'),
        ]);

        return $input;
    }
}
