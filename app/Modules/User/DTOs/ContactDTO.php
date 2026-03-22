<?php

declare(strict_types=1);

namespace App\Modules\User\DTOs;

use App\Modules\User\Enums\ContactType;

final readonly class ContactDTO
{
    public function __construct(
        public ContactType $type,
        public string $value,
    ) {}
}
