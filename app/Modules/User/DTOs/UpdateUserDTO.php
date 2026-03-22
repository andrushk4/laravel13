<?php

declare(strict_types=1);

namespace App\Modules\User\DTOs;

use App\Modules\User\Enums\UserStatus;

final readonly class UpdateUserDTO
{
    /**
     * @param  array<int, ContactDTO>|null  $contacts
     */
    public function __construct(
        public ?string $name = null,
        public ?string $surname = null,
        public ?string $patronymic = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?UserStatus $status = null,
        public ?array $contacts = null,
    ) {}
}
