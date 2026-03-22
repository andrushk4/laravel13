<?php

declare(strict_types=1);

namespace App\Modules\User\Requests;

use App\Modules\User\DTOs\ContactDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Modules\User\Enums\ContactType;
use App\Modules\User\Enums\UserStatus;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

final class UpdateUserRequest extends FormRequest
{
    /**
     * @return array<string, array<int, Enum|Password|string>>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        $userId = $user instanceof User ? $user->id : 0;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'surname' => ['sometimes', 'nullable', 'string', 'max:255'],
            'patronymic' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$userId],
            'password' => ['sometimes', 'string', Password::min(8)],
            'status' => ['sometimes', 'string', new Enum(UserStatus::class)],
            'contacts' => ['sometimes', 'array'],
            'contacts.*.type' => ['required', 'string', new Enum(ContactType::class)],
            'contacts.*.value' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDTO(): UpdateUserDTO
    {
        /** @var array<string, mixed> $validated */
        $validated = $this->validated();

        /** @var array<int, array{type: string, value: string}>|null $contacts */
        $contacts = $validated['contacts'] ?? null;

        return new UpdateUserDTO(
            name: isset($validated['name']) ? (string) $validated['name'] : null,
            surname: isset($validated['surname']) ? (string) $validated['surname'] : null,
            patronymic: isset($validated['patronymic']) ? (string) $validated['patronymic'] : null,
            email: isset($validated['email']) ? (string) $validated['email'] : null,
            password: isset($validated['password']) ? (string) $validated['password'] : null,
            status: isset($validated['status'])
                ? UserStatus::from((string) $validated['status'])
                : null,
            contacts: $contacts !== null
                ? array_map(
                    static fn (array $contact): ContactDTO => new ContactDTO(
                        type: ContactType::from($contact['type']),
                        value: $contact['value'],
                    ),
                    $contacts,
                )
                : null,
        );
    }
}
