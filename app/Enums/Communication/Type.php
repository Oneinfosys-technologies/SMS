<?php

namespace App\Enums\Communication;

use App\Concerns\HasEnum;

enum Type: string
{
    use HasEnum;

    case SMS = 'sms';
    case EMAIL = 'email';

    public static function translation(): string
    {
        return 'communication.types.';
    }
}
