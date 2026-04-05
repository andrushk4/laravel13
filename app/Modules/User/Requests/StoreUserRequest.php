<?php

declare(strict_types=1);

namespace App\Modules\User\Requests;

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
            'contacts.*.order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
