<?php

namespace App\Enums\Resource;

use App\Concerns\HasEnum;

enum OnlineClassPlatform: string
{
    use HasEnum;

    case GOOGLE_MEET = 'google_meet';
    case ZOOM = 'zoom';
    // case MICROSOFT_TEAM = 'microsoft_team';

    public static function translation(): string
    {
        return 'resource.online_class.platforms.';
    }

    public function url(): string
    {
        return match ($this) {
            self::GOOGLE_MEET => 'https://meet.google.com/',
            self::ZOOM => 'zoom.us/j/',
            // self::MICROSOFT_TEAM => 'danger',
        };
    }
}
