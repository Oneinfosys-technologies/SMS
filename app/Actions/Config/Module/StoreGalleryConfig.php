<?php

namespace App\Actions\Config\Module;

class StoreGalleryConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
        ], [], [
        ]);

        return $input;
    }
}
