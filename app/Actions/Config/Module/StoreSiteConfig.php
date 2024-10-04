<?php

namespace App\Actions\Config\Module;

class StoreSiteConfig
{
    public static function handle(): array
    {
        $input = request()->validate([
            'enable_site' => 'boolean',
            'show_public_view' => 'boolean',
        ], [], [
            'enable_site' => __('site.site'),
            'show_public_view' => __('site.config.props.public_view'),
        ]);

        return $input;
    }
}
