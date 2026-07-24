<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'midtrans_order_id' => 'PAY-' . fake()->unique()->numerify('########'),
            'transaction_id' => null,
            'gross_amount' => 220000,
            'payment_type' => null,
            'transaction_status' => 'Pending',
            'fraud_status' => null,
            'snap_token' => 'dummy-token',
            'payload' => [],
            'expires_at' => now()->addMinutes(15),
        ];
    }
    public function settlement(): static
    {
        return $this->state(fn() => [
            'transaction_status' => 'settlement',
        ]);
    }

    public function capture(): static
    {
        return $this->state(fn() => [
            'transaction_status' => 'capture',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn() => [
            'transaction_status' => 'Pending',
        ]);
    }
}
