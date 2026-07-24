<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Midtrans\Snap;
use Tests\TestCase;

class PaymentTest extends TestCase
{
  
    public function test_user_reuses_existing_pending_payment(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'transaction_status' => 'Pending',
            'snap_token' => 'existing-token',
            'expires_at' => now()->addMinutes(10),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/payment/{$order->id}");

        $response->assertOk()
            ->assertJson([
                'payment_id' => $payment->id,
                'snap_token' => 'existing-token',
            ]);
    }
    public function test_user_cannot_pay_other_users_order(): void
    {
        $owner = User::factory()->create([
            'role' => 'user'
        ]);

        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => 'Pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/payment/{$order->id}");

        $response->assertForbidden();
    }
    public function test_user_cannot_pay_non_pending_order(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'Paid',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/payment/{$order->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Order tidak bisa dibayar'
            ]);
    }
}
