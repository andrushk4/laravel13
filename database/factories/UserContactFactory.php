<?php

namespace Database\Factories;

use App\Modules\User\Enums\ContactType;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserContact>
 */
class UserContactFactory extends Factory
{
    protected $model = UserContact::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(ContactType::cases()),
            'value' => fake()->phoneNumber(),
        ];
    }

    public function phone(): static
    {
        return $this->state(fn () => [
            'type' => ContactType::Phone,
            'value' => fake()->phoneNumber(),
        ]);
    }

    public function address(): static
    {
        return $this->state(fn () => [
            'type' => ContactType::Address,
            'value' => fake()->address(),
        ]);
    }

    public function telegram(): static
    {
        return $this->state(fn () => [
            'type' => ContactType::Telegram,
            'value' => '@'.fake()->userName(),
        ]);
    }
}
