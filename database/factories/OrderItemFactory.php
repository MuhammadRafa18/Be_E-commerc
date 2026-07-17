<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'order_id' => Order::factory(),

            'product_id' => Product::factory(),

            'product_sku_id' => ProductSku::factory(),

            'product_title' => fake()->words(2, true),

            'product_size' => 'M',

            'product_image' => 'product.jpg',

            'product_price' => 200000,

            'qty' => 2,

            'subtotal' => 400000,
        ];
    }
}
