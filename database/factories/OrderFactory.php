<?php

namespace Database\Factories;

use App\Models\Addres;
use App\Models\User;
use App\Models\ZoneRegion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'user_id' => User::factory(),

            'address_id' => Addres::factory(),

            'zones_region_id' => ZoneRegion::factory(),

            'shipping_name' => fake()->name(),

            'shipping_phone' => fake()->phoneNumber(),

            'shipping_street' => fake()->streetAddress(),

            'shipping_city' => fake()->city(),

            'shipping_province' => fake()->state(),

            'subtotal' => 200000,

            'diskon' => 50000,

            'ongkir' => 20000,

            'total' => 220000,

            'status' => 'Pending',

            'trackingNumber' => null,

            'estimated_delivery_min' => now()->addDays(fake()->numberBetween(1, 3)),
            'estimated_delivery_max' => now()->addDays(fake()->numberBetween(4, 7)),

            'completed_at' => null,

            'stock_reduced_at' => null,
        ];
    }
    public function paid(): static
    {
        return $this->state(fn() => [
            'status' => 'Paid',
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn() => [
            'status' => 'Diproses',
        ]);
    }

    public function shipped(): static
    {
        return $this->state(fn() => [
            'status' => 'Dikirim',
            'trackingNumber' => 'JNE123456789',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn() => [
            'status' => 'Selesai',
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn() => [
            'status' => 'Canceled',
        ]);
    }
}
