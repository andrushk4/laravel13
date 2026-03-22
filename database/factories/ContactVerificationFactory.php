<?php

namespace Database\Factories;

use App\Modules\User\Enums\VerificationStatus;
use App\Modules\User\Models\ContactVerification;
use App\Modules\User\Models\UserContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactVerification>
 */
class ContactVerificationFactory extends Factory
{
    protected $model = ContactVerification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_contact_id' => UserContact::factory(),
            'code' => (string) fake()->randomNumber(6, true),
            'status' => VerificationStatus::Pending,
            'verified_at' => null,
            'expires_at' => now()->addMinutes(30),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'status' => VerificationStatus::Verified,
            'verified_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => VerificationStatus::Expired,
            'expires_at' => now()->subMinutes(5),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => VerificationStatus::Failed,
        ]);
    }
}
