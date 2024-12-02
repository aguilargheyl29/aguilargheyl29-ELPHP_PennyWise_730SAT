<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->userName, // Map to your `username` column
            'userEmail' => $this->faker->unique()->safeEmail, // Map to your `userEmail` column
            'userPassword' => bcrypt('password'), // Map to your `userPassword` column
            'userFullName' => $this->faker->name, // Map to your `userFullName` column
            'userImage' => $this->faker->imageUrl(200, 200, 'people', true, 'User'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
