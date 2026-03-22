<?php

use App\Modules\User\Enums\ContactType;
use App\Modules\User\Models\ContactVerification;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserContact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('belongs to a user', function () {
    $contact = UserContact::factory()->create();

    expect($contact->user())->toBeInstanceOf(BelongsTo::class)
        ->and($contact->user)->toBeInstanceOf(User::class);
});

it('has verifications relationship', function () {
    $contact = UserContact::factory()->create();

    expect($contact->verifications())->toBeInstanceOf(HasMany::class);
});

it('can create verifications through relationship', function () {
    $contact = UserContact::factory()->create();

    $verification = $contact->verifications()->create([
        'code' => '123456',
        'status' => 'pending',
        'expires_at' => now()->addMinutes(30),
    ]);

    expect($verification)->toBeInstanceOf(ContactVerification::class)
        ->and($contact->verifications)->toHaveCount(1);
});

it('casts type to ContactType enum', function () {
    $contact = UserContact::factory()->phone()->create();

    expect($contact->type)->toBeInstanceOf(ContactType::class)
        ->and($contact->type)->toBe(ContactType::Phone);
});

it('has phone factory state', function () {
    $contact = UserContact::factory()->phone()->create();

    expect($contact->type)->toBe(ContactType::Phone);
});

it('has address factory state', function () {
    $contact = UserContact::factory()->address()->create();

    expect($contact->type)->toBe(ContactType::Address);
});

it('has telegram factory state', function () {
    $contact = UserContact::factory()->telegram()->create();

    expect($contact->type)->toBe(ContactType::Telegram);
});

it('is deleted when parent user is deleted', function () {
    $user = User::factory()->create();
    UserContact::factory()->create(['user_id' => $user->id]);

    $user->delete();

    expect(UserContact::query()->where('user_id', $user->id)->count())->toBe(0);
});
