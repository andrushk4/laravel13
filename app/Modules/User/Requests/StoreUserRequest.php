<?php

declare(strict_types=1);

namespace App\Modules\User\Requests;

use App\Modules\User\DTOs\ContactDTO;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\Enums\ContactType;
use App\Modules\User\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

final class StoreUserRequest extends FormRequest
{
    /**
     * @return array<string, array<int, Enum|Password|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'patronymic' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'status' => ['sometimes', 'string', new Enum(UserStatus::class)],
            'contacts' => ['sometimes', 'array'],
            'contacts.*.type' => ['required', 'string', new Enum(ContactType::class)],
            'contacts.*.value' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDTO(): CreateUserDTO
    {
        /** @var string $name */
        $name = $this->validated('name');
        /** @var string $email */
        $email = $this->validated('email');
        /** @var string $password */
        $password = $this->validated('password');
        /** @var string|null $surname */
        $surname = $this->validated('surname');
        /** @var string|null $patronymic */
        $patronymic = $this->validated('patronymic');
        /** @var string|null $statusValue */
        $statusValue = $this->validated('status');
        /** @var array<int, array{type: string, value: string}> $contacts */
        $contacts = $this->validated('contacts', []);

        return new CreateUserDTO(
            name: $name,
            email: $email,
            password: $password,
            surname: $surname,
            patronymic: $patronymic,
            status: $statusValue !== null
                ? UserStatus::from($statusValue)
                : UserStatus::Created,
            contacts: array_map(
                static fn (array $contact): ContactDTO => new ContactDTO(
                    type: ContactType::from($contact['type']),
                    value: $contact['value'],
                ),
                $contacts,
            ),
        );
    }
}
