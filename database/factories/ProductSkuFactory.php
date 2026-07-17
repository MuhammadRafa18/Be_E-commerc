<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSku>
 */
class ProductSkuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
          return [
            'product_id' => Product::factory(),
            'price' => fake()->numberBetween(100000, 300000),
            'sell_price' => fake()->numberBetween(80000, 250000),
            'stock' => fake()->numberBetween(5, 100),
            'weight_gram' => fake()->numberBetween(100, 1000),
        ];
    }
}
