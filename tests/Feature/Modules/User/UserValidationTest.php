<?php

use App\Modules\User\Models\User;

describe('store validation', function () {
    it('requires name', function () {
        $response = $this->postJson('/api/users', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('requires email', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('requires valid email format', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('requires unique email', function () {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('requires password', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    });

    it('requires password confirmation', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    });

    it('requires password minimum 8 characters', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    });

    it('validates status must be a valid enum value', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'invalid_status',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    });

    it('validates name max length', function () {
        $response = $this->postJson('/api/users', [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates contact type must be a valid enum', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'contacts' => [
                ['type' => 'invalid_type', 'value' => '123'],
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['contacts.0.type']);
    });

    it('validates contact value is required when contacts are provided', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'contacts' => [
                ['type' => 'phone'],
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['contacts.0.value']);
    });

    it('accepts valid status values', function () {
        $this->postJson('/api/users', [
            'name' => 'Test1',
            'email' => 'test1@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'created',
        ])->assertCreated();

        $this->postJson('/api/users', [
            'name' => 'Test2',
            'email' => 'test2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
        ])->assertCreated();

        $this->postJson('/api/users', [
            'name' => 'Test3',
            'email' => 'test3@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'blocked',
        ])->assertCreated();
    });

    it('accepts valid contact types', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'contacts' => [
                ['type' => 'phone', 'value' => '+1234567890'],
                ['type' => 'address', 'value' => '123 Main St'],
                ['type' => 'telegram', 'value' => '@test'],
            ],
        ]);

        $response->assertCreated();
    });
});

describe('update validation', function () {
    it('validates email uniqueness excluding the current user', function () {
        $user = User::factory()->create(['email' => 'first@example.com']);
        User::factory()->create(['email' => 'second@example.com']);

        $response = $this->putJson("/api/users/{$user->id}", [
            'email' => 'second@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('allows updating with same email for the same user', function () {
        $user = User::factory()->create(['email' => 'same@example.com']);

        $response = $this->putJson("/api/users/{$user->id}", [
            'email' => 'same@example.com',
        ]);

        $response->assertSuccessful();
    });

    it('allows partial updates without requiring all fields', function () {
        $user = User::factory()->create(['name' => 'Original']);

        $response = $this->putJson("/api/users/{$user->id}", [
            'surname' => 'NewSurname',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('data.name', 'Original')
            ->assertJsonPath('data.surname', 'NewSurname');
    });

    it('validates status must be a valid enum value', function () {
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'status' => 'nonexistent',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    });

    it('validates contact type in update', function () {
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'contacts' => [
                ['type' => 'fax', 'value' => '123'],
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['contacts.0.type']);
    });

    it('does not require password confirmation on update', function () {
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'password' => 'newpassword123',
        ]);

        $response->assertSuccessful();
    });
});
