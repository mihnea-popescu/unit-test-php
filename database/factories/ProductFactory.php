<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id,
            'name' => fake()->text(200),
            'description' => fake()->text(200),
            'stock' => fake()->numberBetween(1, 50),
            'price' => fake()->randomFloat(2, 0.5, 40),
            'sale_price' => fake()->randomFloat(2, 0.5, 40),
        ];
    }
}
