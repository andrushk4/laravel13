<?php

use App\Modules\User\DTOs\ContactDTO;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\Enums\ContactType;
use App\Modules\User\Enums\UserStatus;

it('creates with required fields and defaults', function () {
    $dto = new CreateUserDTO(
        name: 'John',
        email: 'john@example.com',
        password: 'secret123',
    );

    expect($dto->name)->toBe('John')
        ->and($dto->email)->toBe('john@example.com')
        ->and($dto->password)->toBe('secret123')
        ->and($dto->surname)->toBeNull()
        ->and($dto->patronymic)->toBeNull()
        ->and($dto->status)->toBe(UserStatus::Created)
        ->and($dto->contacts)->toBe([]);
});

it('creates with all fields populated', function () {
    $contact = new ContactDTO(
        type: ContactType::Phone,
        value: '+1234567890',
    );

    $dto = new CreateUserDTO(
        name: 'John',
        email: 'john@example.com',
        password: 'secret123',
        surname: 'Doe',
        patronymic: 'Smith',
        status: UserStatus::Active,
        contacts: [$contact],
    );

    expect($dto->surname)->toBe('Doe')
        ->and($dto->patronymic)->toBe('Smith')
        ->and($dto->status)->toBe(UserStatus::Active)
        ->and($dto->contacts)->toHaveCount(1)
        ->and($dto->contacts[0]->type)->toBe(ContactType::Phone)
        ->and($dto->contacts[0]->value)->toBe('+1234567890');
});

it('is readonly', function () {
    $dto = new CreateUserDTO(
        name: 'John',
        email: 'john@example.com',
        password: 'secret',
    );

    expect(fn () => $dto->name = 'Jane')
        ->toThrow(Error::class);
});
