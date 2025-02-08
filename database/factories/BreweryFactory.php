<?php

namespace Database\Factories;

use App\Enums\BreweryType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brewery>
 */
class BreweryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'name' => fake()->company(),
            'type' => fake()->randomElement(BreweryType::cases()),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'country' => fake()->countryCode(),
            'address_1' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'website_url' => fake()->url(),
            'phone_number' => fake()->phoneNumber(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }

    /**
     * Set the brewery as a micro brewery.
     */
    public function micro(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'micro',
        ]);
    }

    /**
     * Set the brewery as a regional brewery.
     */
    public function regional(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'regional',
        ]);
    }

    /**
     * Set the brewery as closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'closed',
        ]);
    }
}
