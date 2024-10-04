<?php

namespace App\Enums\Employee\Leave;

use App\Concerns\HasEnum;
use App\Contracts\HasColor;

enum RequestStatus: string implements HasColor
{
    use HasEnum;

    case REQUESTED = 'requested';
    case REJECTED = 'rejected';
    case APPROVED = 'approved';
    case WITHDRAWN = 'withdrawn';

    public static function translation(): string
    {
        return 'employee.leave.request.statuses.';
    }

    public function color(): string
    {
        return match ($this) {
            RequestStatus::REQUESTED => 'info',
            RequestStatus::REJECTED => 'danger',
            RequestStatus::APPROVED => 'success',
            RequestStatus::WITHDRAWN => 'warning',
        };
    }
}
