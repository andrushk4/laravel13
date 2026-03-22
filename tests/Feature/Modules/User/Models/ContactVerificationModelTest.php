<?php

use App\Modules\User\Enums\VerificationStatus;
use App\Modules\User\Models\ContactVerification;
use App\Modules\User\Models\UserContact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

it('belongs to a user contact', function () {
    $verification = ContactVerification::factory()->create();

    expect($verification->contact())->toBeInstanceOf(BelongsTo::class)
        ->and($verification->contact)->toBeInstanceOf(UserContact::class);
});

it('casts status to VerificationStatus enum', function () {
    $verification = ContactVerification::factory()->create();

    expect($verification->status)->toBeInstanceOf(VerificationStatus::class)
        ->and($verification->status)->toBe(VerificationStatus::Pending);
});

it('casts verified_at to datetime', function () {
    $verification = ContactVerification::factory()->verified()->create();

    expect($verification->verified_at)->toBeInstanceOf(Carbon::class);
});

it('casts expires_at to datetime', function () {
    $verification = ContactVerification::factory()->create();

    expect($verification->expires_at)->toBeInstanceOf(Carbon::class);
});

it('has verified factory state', function () {
    $verification = ContactVerification::factory()->verified()->create();

    expect($verification->status)->toBe(VerificationStatus::Verified)
        ->and($verification->verified_at)->not->toBeNull();
});

it('has expired factory state', function () {
    $verification = ContactVerification::factory()->expired()->create();

    expect($verification->status)->toBe(VerificationStatus::Expired)
        ->and($verification->expires_at->isPast())->toBeTrue();
});

it('has failed factory state', function () {
    $verification = ContactVerification::factory()->failed()->create();

    expect($verification->status)->toBe(VerificationStatus::Failed);
});

it('is deleted when parent contact is deleted', function () {
    $contact = UserContact::factory()->create();
    ContactVerification::factory()->create(['user_contact_id' => $contact->id]);

    $contact->delete();

    expect(ContactVerification::query()->where('user_contact_id', $contact->id)->count())->toBe(0);
});
