<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductSku;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'              => User::factory(),
            'product_id'           => Product::factory(),
            'product_sku_id'       => ProductSku::factory(),
            'product_fashion_id'   => null,
            'product_skincare_id'  => null,
            'qty'                  => fake()->numberBetween(1, 3),
            'is_selected'          => true,
        ];
    }
    public function selected(): static
    {
        return $this->state(fn() => [
            'is_selected' => true,
        ]);
    }

    public function unselected(): static
    {
        return $this->state(fn() => [
            'is_selected' => false,
        ]);
    }
}
