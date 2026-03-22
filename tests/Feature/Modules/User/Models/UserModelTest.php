<?php

use App\Modules\User\Enums\UserStatus;
use App\Modules\User\Models\Role;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserContact;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

it('has contacts relationship', function () {
    $user = User::factory()->create();

    expect($user->contacts())->toBeInstanceOf(HasMany::class);
});

it('has roles relationship', function () {
    $user = User::factory()->create();

    expect($user->roles())->toBeInstanceOf(BelongsToMany::class);
});

it('can create contacts through relationship', function () {
    $user = User::factory()->create();

    $contact = $user->contacts()->create([
        'type' => 'phone',
        'value' => '+1234567890',
    ]);

    expect($contact)->toBeInstanceOf(UserContact::class)
        ->and($user->contacts)->toHaveCount(1);
});

it('can attach roles with pivot data', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();

    $user->roles()->attach($role, ['assigned_at' => now()]);

    expect($user->roles)->toHaveCount(1)
        ->and($user->roles->first()->pivot->assigned_at)->not->toBeNull();
});

it('casts status to UserStatus enum', function () {
    $user = User::factory()->create(['status' => 'active']);

    expect($user->status)->toBeInstanceOf(UserStatus::class)
        ->and($user->status)->toBe(UserStatus::Active);
});

it('casts email_verified_at to datetime', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)->toBeInstanceOf(Carbon::class);
});

it('hashes password automatically', function () {
    $user = User::factory()->create(['password' => 'plaintext']);

    expect($user->password)->not->toBe('plaintext');
});

it('hides password and remember_token in serialization', function () {
    $user = User::factory()->create();
    $array = $user->toArray();

    expect($array)->not->toHaveKey('password')
        ->and($array)->not->toHaveKey('remember_token');
});

it('uses the correct factory', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBeString()
        ->and($user->email)->toBeString();
});

it('has unverified factory state', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
});

it('has fillable attributes via Fillable attribute', function () {
    $user = User::query()->create([
        'name' => 'Test',
        'email' => 'test@test.com',
        'password' => 'password',
        'status' => 'created',
    ]);

    expect($user->name)->toBe('Test')
        ->and($user->email)->toBe('test@test.com')
        ->and($user->status)->toBe(UserStatus::Created);
});
