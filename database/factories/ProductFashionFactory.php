<?php

namespace Database\Factories;

use App\Models\ProductSku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductFashion>
 */
class ProductFashionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'product_sku_id' => ProductSku::factory(),
            'size' => fake()->randomElement(['S','M','L','XL']),
            'color' => fake()->safeColorName(),
        ];
    }
}
