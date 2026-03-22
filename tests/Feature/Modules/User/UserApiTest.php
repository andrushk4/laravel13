<?php

use App\Modules\User\Enums\UserStatus;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserContact;

it('lists users with pagination', function () {
    User::factory()->count(20)->create();

    $response = $this->getJson('/api/users?per_page=10');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'surname', 'email', 'status', 'created_at', 'updated_at'],
            ],
            'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('pagination.per_page', 10)
        ->assertJsonPath('pagination.total', 20)
        ->assertJsonPath('pagination.last_page', 2);
});

it('lists users with default pagination of 15', function () {
    User::factory()->count(3)->create();

    $response = $this->getJson('/api/users');

    $response->assertSuccessful()
        ->assertJsonPath('pagination.per_page', 15);
});

it('lists users sorted by created_at descending by default', function () {
    $first = User::factory()->create(['created_at' => now()->subDay()]);
    $second = User::factory()->create(['created_at' => now()]);

    $response = $this->getJson('/api/users');

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data[0]['id'])->toBe($second->id)
        ->and($data[1]['id'])->toBe($first->id);
});

it('creates a user with minimal data', function () {
    $response = $this->postJson('/api/users', [
        'name' => 'John',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'John')
        ->assertJsonPath('data.email', 'john@example.com')
        ->assertJsonPath('data.status', UserStatus::Created->value);

    $this->assertDatabaseHas('users', [
        'name' => 'John',
        'email' => 'john@example.com',
        'status' => UserStatus::Created->value,
    ]);
});

it('creates a user with full data including contacts', function () {
    $response = $this->postJson('/api/users', [
        'name' => 'John',
        'surname' => 'Doe',
        'patronymic' => 'Smith',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'status' => 'active',
        'contacts' => [
            ['type' => 'phone', 'value' => '+1234567890'],
            ['type' => 'telegram', 'value' => '@johndoe'],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'John')
        ->assertJsonPath('data.surname', 'Doe')
        ->assertJsonPath('data.patronymic', 'Smith')
        ->assertJsonPath('data.status', UserStatus::Active->value);

    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    $this->assertDatabaseHas('user_contacts', [
        'type' => 'phone',
        'value' => '+1234567890',
    ]);
    $this->assertDatabaseHas('user_contacts', [
        'type' => 'telegram',
        'value' => '@johndoe',
    ]);
});

it('creates a user with default status of created', function () {
    $response = $this->postJson('/api/users', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.status', 'created');
});

it('hashes the password when creating a user', function () {
    $this->postJson('/api/users', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::query()->where('email', 'test@example.com')->first();
    expect($user->password)->not->toBe('password123');
});

it('shows a single user with contacts', function () {
    $user = User::factory()->create();
    UserContact::factory()->phone()->create(['user_id' => $user->id]);
    UserContact::factory()->telegram()->create(['user_id' => $user->id]);

    $response = $this->getJson("/api/users/{$user->id}");

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.name', $user->name)
        ->assertJsonCount(2, 'data.contacts');
});

it('returns 404 for non-existent user', function () {
    $response = $this->getJson('/api/users/99999');

    $response->assertNotFound();
});

it('updates a user name', function () {
    $user = User::factory()->create(['name' => 'Old Name']);

    $response = $this->putJson("/api/users/{$user->id}", [
        'name' => 'New Name',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.name', 'New Name');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
});

it('updates a user email with unique validation excluding self', function () {
    $user = User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->putJson("/api/users/{$user->id}", [
        'email' => 'existing@example.com',
    ]);

    $response->assertSuccessful();
});

it('updates a user status', function () {
    $user = User::factory()->create(['status' => UserStatus::Created]);

    $response = $this->putJson("/api/users/{$user->id}", [
        'status' => 'active',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.status', 'active');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'active']);
});

it('updates user contacts by syncing them', function () {
    $user = User::factory()->create();
    UserContact::factory()->phone()->create(['user_id' => $user->id]);

    $response = $this->putJson("/api/users/{$user->id}", [
        'contacts' => [
            ['type' => 'telegram', 'value' => '@newhandle'],
        ],
    ]);

    $response->assertSuccessful();

    expect($user->refresh()->contacts)->toHaveCount(1);
    $this->assertDatabaseHas('user_contacts', [
        'user_id' => $user->id,
        'type' => 'telegram',
        'value' => '@newhandle',
    ]);
    $this->assertDatabaseMissing('user_contacts', [
        'user_id' => $user->id,
        'type' => 'phone',
    ]);
});

it('does not change contacts when contacts field is not sent during update', function () {
    $user = User::factory()->create();
    UserContact::factory()->phone()->create(['user_id' => $user->id, 'value' => '+111']);

    $this->putJson("/api/users/{$user->id}", [
        'name' => 'Updated Name',
    ]);

    expect($user->refresh()->contacts)->toHaveCount(1);
    $this->assertDatabaseHas('user_contacts', ['user_id' => $user->id, 'value' => '+111']);
});

it('deletes a user', function () {
    $user = User::factory()->create();

    $response = $this->deleteJson("/api/users/{$user->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('cascades deleting user contacts when user is deleted', function () {
    $user = User::factory()->create();
    UserContact::factory()->count(2)->create(['user_id' => $user->id]);

    $this->deleteJson("/api/users/{$user->id}");

    $this->assertDatabaseMissing('user_contacts', ['user_id' => $user->id]);
});

it('does not expose password in user response', function () {
    $user = User::factory()->create();

    $response = $this->getJson("/api/users/{$user->id}");

    $response->assertSuccessful()
        ->assertJsonMissing(['password']);
});

it('returns proper structure for created user response', function () {
    $response = $this->postJson('/api/users', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'success',
            'data' => ['id', 'name', 'email', 'status', 'created_at', 'updated_at'],
        ]);
});

it('filters users by status', function () {
    User::factory()->create(['status' => UserStatus::Active]);
    User::factory()->create(['status' => UserStatus::Blocked]);

    $response = $this->getJson('/api/users?filter[status]=active');

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data)->toHaveCount(1)
        ->and($data[0]['status'])->toBe('active');
});

it('filters users by name partially', function () {
    User::factory()->create(['name' => 'Alexander']);
    User::factory()->create(['name' => 'Boris']);

    $response = $this->getJson('/api/users?filter[name]=Alex');

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data)->toHaveCount(1)
        ->and($data[0]['name'])->toBe('Alexander');
});

it('sorts users by name ascending', function () {
    User::factory()->create(['name' => 'Charlie']);
    User::factory()->create(['name' => 'Alice']);

    $response = $this->getJson('/api/users?sort=name');

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data[0]['name'])->toBe('Alice')
        ->and($data[1]['name'])->toBe('Charlie');
});

it('includes contacts relation when requested', function () {
    $user = User::factory()->create();
    UserContact::factory()->phone()->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/users?include=contacts');

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data[0]['contacts'])->toHaveCount(1);
});
