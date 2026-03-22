<?php

use App\Modules\User\DTOs\ContactDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Modules\User\Enums\ContactType;
use App\Modules\User\Enums\UserStatus;

it('creates with all null defaults', function () {
    $dto = new UpdateUserDTO;

    expect($dto->name)->toBeNull()
        ->and($dto->surname)->toBeNull()
        ->and($dto->patronymic)->toBeNull()
        ->and($dto->email)->toBeNull()
        ->and($dto->password)->toBeNull()
        ->and($dto->status)->toBeNull()
        ->and($dto->contacts)->toBeNull();
});

it('creates with partial fields', function () {
    $dto = new UpdateUserDTO(
        name: 'Updated Name',
        status: UserStatus::Blocked,
    );

    expect($dto->name)->toBe('Updated Name')
        ->and($dto->status)->toBe(UserStatus::Blocked)
        ->and($dto->email)->toBeNull()
        ->and($dto->contacts)->toBeNull();
});

it('creates with contacts array', function () {
    $dto = new UpdateUserDTO(
        contacts: [
            new ContactDTO(type: ContactType::Telegram, value: '@test'),
        ],
    );

    expect($dto->contacts)->toHaveCount(1)
        ->and($dto->contacts[0]->type)->toBe(ContactType::Telegram);
});

it('creates with empty contacts array to clear all contacts', function () {
    $dto = new UpdateUserDTO(contacts: []);

    expect($dto->contacts)->toBeArray()
        ->and($dto->contacts)->toBeEmpty();
});

it('is readonly', function () {
    $dto = new UpdateUserDTO(name: 'Test');

    expect(fn () => $dto->name = 'Changed')
        ->toThrow(Error::class);
});
