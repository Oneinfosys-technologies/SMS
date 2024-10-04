<?php

namespace App\Enums\Academic;

use App\Concerns\HasEnum;

enum ProgramType: string
{
    use HasEnum;

    case K12 = 'k12';
    case DIPLOMA = 'diploma';
    case UNDER_GRADUATE = 'under_graduate';
    case POST_GRADUATE = 'post_graduate';
    case RESEARCH = 'research';

    public static function translation(): string
    {
        return 'academic.program.types.';
    }
}
