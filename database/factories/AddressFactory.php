<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\AddressType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'addressable_type' => null,  // To be set by specific model factories
            'addressable_id' => null,  // To be set by specific model factories
            'name' => $this->faker->optional(0.3)->sentence(3),
            'numero' => $this->faker->optional(0.7)->bothify('##?'),
            'street' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'zip_code' => $this->faker->postcode(),
            'address_type_id' => AddressType::inRandomOrder()->first()?->id ?? AddressType::factory(),
        ];
    }

    public function forModel($model): static
    {
        return $this->state(fn(array $attributes) => [
            'addressable_type' => get_class($model),
            'addressable_id' => $model->getKey(),
        ]);
    }

    public function forPerson(): static
    {
        return $this->state(fn(array $attributes) => [
            'address_type_id' => AddressType::forPersons()->inRandomOrder()->first()?->id
                ?? AddressType::factory()->forPerson(),
        ]);
    }

    public function forCompany(): static
    {
        return $this->state(fn(array $attributes) => [
            'address_type_id' => AddressType::forCompanies()->inRandomOrder()->first()?->id
                ?? AddressType::factory()->forCompany(),
        ]);
    }

    public function residential(): static
    {
        return $this->state(fn(array $attributes) => [
            'address_type_id' => AddressType::where('name', 'Residenza')->first()?->id
                ?? AddressType::factory()->forPerson(),
        ]);
    }

    public function legal(): static
    {
        return $this->state(fn(array $attributes) => [
            'address_type_id' => AddressType::where('name', 'Sede Legale')->first()?->id
                ?? AddressType::factory()->forCompany(),
        ]);
    }
}
