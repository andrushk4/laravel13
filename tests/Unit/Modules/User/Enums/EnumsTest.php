<?php

use App\Modules\User\Enums\ContactType;
use App\Modules\User\Enums\UserStatus;
use App\Modules\User\Enums\VerificationStatus;

describe('UserStatus', function () {
    it('has expected cases', function () {
        expect(UserStatus::cases())->toHaveCount(3)
            ->and(UserStatus::Created->value)->toBe('created')
            ->and(UserStatus::Active->value)->toBe('active')
            ->and(UserStatus::Blocked->value)->toBe('blocked');
    });

    it('can be created from string value', function () {
        expect(UserStatus::from('created'))->toBe(UserStatus::Created)
            ->and(UserStatus::from('active'))->toBe(UserStatus::Active)
            ->and(UserStatus::from('blocked'))->toBe(UserStatus::Blocked);
    });

    it('throws on invalid value', function () {
        expect(fn () => UserStatus::from('invalid'))
            ->toThrow(ValueError::class);
    });
});

describe('ContactType', function () {
    it('has expected cases', function () {
        expect(ContactType::cases())->toHaveCount(3)
            ->and(ContactType::Phone->value)->toBe('phone')
            ->and(ContactType::Address->value)->toBe('address')
            ->and(ContactType::Telegram->value)->toBe('telegram');
    });

    it('can be created from string value', function () {
        expect(ContactType::from('phone'))->toBe(ContactType::Phone)
            ->and(ContactType::from('address'))->toBe(ContactType::Address)
            ->and(ContactType::from('telegram'))->toBe(ContactType::Telegram);
    });

    it('throws on invalid value', function () {
        expect(fn () => ContactType::from('fax'))
            ->toThrow(ValueError::class);
    });
});

describe('VerificationStatus', function () {
    it('has expected cases', function () {
        expect(VerificationStatus::cases())->toHaveCount(4)
            ->and(VerificationStatus::Pending->value)->toBe('pending')
            ->and(VerificationStatus::Verified->value)->toBe('verified')
            ->and(VerificationStatus::Failed->value)->toBe('failed')
            ->and(VerificationStatus::Expired->value)->toBe('expired');
    });

    it('can be created from string value', function () {
        expect(VerificationStatus::from('pending'))->toBe(VerificationStatus::Pending)
            ->and(VerificationStatus::from('verified'))->toBe(VerificationStatus::Verified)
            ->and(VerificationStatus::from('failed'))->toBe(VerificationStatus::Failed)
            ->and(VerificationStatus::from('expired'))->toBe(VerificationStatus::Expired);
    });

    it('throws on invalid value', function () {
        expect(fn () => VerificationStatus::from('cancelled'))
            ->toThrow(ValueError::class);
    });
});
