<?php

namespace App\Actions\Config\Module;

class StoreExamConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
            'marksheet_format' => 'sometimes|string|max:50|in:India,Cameroon',
        ], [], [
            'marksheet_format' => __('exam.config.props.marksheet_format'),
        ]);

        return $input;
    }
}
