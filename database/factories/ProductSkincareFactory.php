<?php

namespace Database\Factories;

use App\Models\ProductSku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSkincare>
 */
class ProductSkincareFactory extends Factory
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
            'size' => fake()->randomElement(['50 ml','100 ml']),
            'use_produk' => fake()->sentence(),
            'ingredient' => fake()->sentence(),
        ];
    }
}
