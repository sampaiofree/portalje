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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'telefone_pessoal_1' => '556299999' . fake()->numerify('####'),
            'telefone_pessoal_1_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
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

    public function unverifiedPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'telefone_pessoal_1' => null,
            'telefone_pessoal_1_pending' => null,
            'telefone_pessoal_1_verified_at' => null,
            'telefone_pessoal_1_verification_code_hash' => null,
            'telefone_pessoal_1_verification_code_expires_at' => null,
            'telefone_pessoal_1_verification_code_sent_at' => null,
        ]);
    }
}
