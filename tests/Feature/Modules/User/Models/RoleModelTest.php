<?php

use App\Modules\User\Models\Role;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;

it('has users relationship', function () {
    $role = Role::factory()->create();

    expect($role->users())->toBeInstanceOf(BelongsToMany::class);
});

it('can be attached to users with pivot data', function () {
    $role = Role::factory()->create();
    $user = User::factory()->create();

    $role->users()->attach($user, ['assigned_at' => now()]);

    expect($role->users)->toHaveCount(1)
        ->and($role->users->first()->id)->toBe($user->id);
});

it('stores assigned_at pivot field', function () {
    $role = Role::factory()->create();
    $user = User::factory()->create();
    $now = now();

    $role->users()->attach($user, ['assigned_at' => $now]);

    $pivot = $role->users->first()->pivot;
    expect($pivot->assigned_at)->not->toBeNull();
});

it('has fillable attributes', function () {
    $role = Role::query()->create([
        'name' => 'Admin',
        'slug' => 'admin',
        'description' => 'Administrator role',
    ]);

    expect($role->name)->toBe('Admin')
        ->and($role->slug)->toBe('admin')
        ->and($role->description)->toBe('Administrator role');
});

it('enforces unique slug in database', function () {
    Role::factory()->create(['slug' => 'admin']);

    expect(fn () => Role::factory()->create(['slug' => 'admin']))
        ->toThrow(QueryException::class);
});

it('enforces unique user-role pair in pivot table', function () {
    $role = Role::factory()->create();
    $user = User::factory()->create();

    $role->users()->attach($user, ['assigned_at' => now()]);

    expect(fn () => $role->users()->attach($user, ['assigned_at' => now()]))
        ->toThrow(QueryException::class);
});
