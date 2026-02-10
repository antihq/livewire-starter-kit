<?php

namespace Database\Factories;

use App\Models\Marketplace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'marketplace_id' => Marketplace::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => (int) round(fake()->randomFloat(2, 1, 1000) * 100),
        ];
    }
}
