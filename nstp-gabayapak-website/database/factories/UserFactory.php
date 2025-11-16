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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_Name' => fake()->name(),
            'user_Email' => fake()->unique()->safeEmail(),
            'user_Email_verified_at' => now(),
            'user_Password' => Hash::make('password'), // password
            'user_Type' => fake()->randomElement(['student', 'staff']),
            'user_role' => fake()->randomElement(['Student', 'NSTP Formator', 'NSTP Program Officer', 'SACSI Director', 'SACSI Admin Staff']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_Email_verified_at' => null,
        ]);
    }
}