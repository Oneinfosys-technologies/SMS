<?php

namespace App\Actions\Config\Module;

class StoreAcademicConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
            'period_selection' => ['required', 'string', 'in:period_wise,session_wise'],
        ], [], [
            'period_selection' => trans('academic.period_selection'),
        ]);

        return $input;
    }
}
