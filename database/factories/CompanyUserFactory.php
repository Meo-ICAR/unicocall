<?php

namespace Database\Factories;

use App\Models\CompanyUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyUser>
 */
class CompanyUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => $this->faker->randomElement(['utente', 'amministratore', 'superamministratore']),
        ];
    }

    /**
     * Create a superadmin company user
     */
    public function superamministratore(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'superamministratore',
        ]);
    }

    /**
     * Create an admin company user
     */
    public function amministratore(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'amministratore',
        ]);
    }

    /**
     * Create a regular user company user
     */
    public function utente(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'utente',
        ]);
    }
}
