<?php

declare(strict_types=1);

namespace App\Modules\User\DTOs;

use App\Modules\User\Enums\UserStatus;

final readonly class CreateUserDTO
{
    /**
     * @param  array<int, ContactDTO>  $contacts
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $surname = null,
        public ?string $patronymic = null,
        public UserStatus $status = UserStatus::Created,
        public array $contacts = [],
    ) {}
}
