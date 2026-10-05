<?php

namespace Database\Factories;

use App\Models\AddressType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AddressType>
 */
class AddressTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            ['name' => 'Residenza', 'is_person' => true],
            ['name' => 'Domicilio', 'is_person' => true],
            ['name' => 'Domicilio Legale', 'is_person' => false],
            ['name' => 'Domicilio Operativo', 'is_person' => false],
            ['name' => 'Sede Legale', 'is_person' => false],
            ['name' => 'Sede Operativa', 'is_person' => false],
        ];

        return $this->faker->randomElement($types);
    }

    public function forPerson(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_person' => true,
            'name' => $this->faker->randomElement(['Residenza', 'Domicilio']),
        ]);
    }

    public function forCompany(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_person' => false,
            'name' => $this->faker->randomElement([
                'Domicilio Legale',
                'Domicilio Operativo',
                'Sede Legale',
                'Sede Operativa'
            ]),
        ]);
    }
}
