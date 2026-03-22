<?php

declare(strict_types=1);

namespace App\Modules\User\Enums;

enum UserStatus: string
{
    case Created = 'created';
    case Active = 'active';
    case Blocked = 'blocked';
}
