<?php

declare(strict_types=1);

namespace App\Modules\User\Enums;

enum ContactType: string
{
    case Phone = 'phone';
    case Address = 'address';
    case Telegram = 'telegram';
}
