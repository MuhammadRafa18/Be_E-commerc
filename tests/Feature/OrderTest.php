<?php

namespace Tests\Feature;

use App\Models\Addres;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductFashion;
use App\Models\ProductSku;
use App\Models\ShippingZone;
use App\Models\User;
use App\Models\ZoneRegion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_user_can_checkout()
    {


        $user = User::factory()->create();

        Sanctum::actingAs($user);


        $address = Addres::factory()->create([
            'user_id' => $user->id,
        ]);


        $shipping = ShippingZone::factory()->create([
            'price' => 20000,
        ]);

        $region = ZoneRegion::factory()->create([
            'shipping_zone_id' => $shipping->id,
        ]);


        $category = Category::factory()->fashion()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $sku = ProductSku::factory()->create([
            'product_id' => $product->id,
            'price' => 120000,
            'sell_price' => 100000,
            'stock' => 10,
        ]);

        $fashion = ProductFashion::factory()->create([
            'product_sku_id' => $sku->id,
        ]);

        Cart::factory()->selected()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_sku_id' => $sku->id,
            'product_fashion_id' => $fashion->id,
            'qty' => 2,
        ]);

        $response = $this->postJson('/api/order', [
            'address_id' => $address->id,
            'zones_region_id' => $region->id,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'Pending',
            'subtotal' => 200000,
            'diskon' => 40000,
            'ongkir' => 20000,
            'total' => 220000,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'qty' => 2,
            'subtotal' => 200000,
        ]);

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
        ]);
    }
    public function test_user_cannot_checkout_without_phone()
    {
        $user = User::factory()
            ->withoutPhone()
            ->create();

        Sanctum::actingAs($user);

        $address = Addres::factory()->create([
            'user_id' => $user->id,
        ]);

        $shipping = ShippingZone::factory()->create();

        $region = ZoneRegion::factory()->create([
            'shipping_zone_id' => $shipping->id,
        ]);

        $response = $this->postJson('/api/order', [
            'address_id' => $address->id,
            'zones_region_id' => $region->id,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Verif Nomor Telephone untuk Membuat Order'
            ]);
    }
    public function test_user_cannot_checkout_when_cart_empty()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $address = Addres::factory()->create([
            'user_id' => $user->id,
        ]);

        $shipping = ShippingZone::factory()->create();

        $region = ZoneRegion::factory()->create([
            'shipping_zone_id' => $shipping->id,
        ]);

        $response = $this->postJson('/api/order', [
            'address_id' => $address->id,
            'zones_region_id' => $region->id,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Pilih produk dulu'
            ]);
    }
    public function test_user_cannot_checkout_using_other_user_address()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $otherUser = User::factory()->create();

        $address = Addres::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $shipping = ShippingZone::factory()->create();

        $region = ZoneRegion::factory()->create([
            'shipping_zone_id' => $shipping->id,
        ]);

        $response = $this->postJson('/api/order', [
            'address_id' => $address->id,
            'zones_region_id' => $region->id,
        ]);

        $response->assertStatus(500);

        $response->assertJson([
            'message' => 'Alamat tidak ditemukan'
        ]);
    }
    public function test_user_cannot_checkout_with_invalid_region()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $address = Addres::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->postJson('/api/order', [
            'address_id' => $address->id,
            'zones_region_id' => 999
        ]);

        $response->assertStatus(422);
    }
    public function test_user_cannot_checkout_when_stock_not_enough()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $address = Addres::factory()->create([
            'user_id' => $user->id,
        ]);

        $zone = ShippingZone::factory()->create([
            'price' => 10000,
        ]);

        $region = ZoneRegion::factory()->create([
            'shipping_zone_id' => $zone->id,
        ]);

        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $sku = ProductSku::factory()->create([
            'product_id' => $product->id,
            'stock' => 1, // stok sedikit
            'price' => 100000,
            'sell_price' => 90000,
        ]);

        $fashion = ProductFashion::factory()->create([
            'product_sku_id' => $sku->id,
        ]);

        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_sku_id' => $sku->id,
            'product_fashion_id' => $fashion->id,
            'qty' => 5, // lebih besar dari stok
            'is_selected' => true,
        ]);

        $response = $this->postJson('/api/order', [
            'address_id' => $address->id,
            'zones_region_id' => $region->id,
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'message' => "Gagal Checkout: Stok produk {$product->title} tidak mencukupi atau sudah habis."
            ]);

        $this->assertDatabaseCount('orders', 0);
    }
    public function test_user_can_view_his_order()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/order/{$order->id}");

        $response->assertOk();

        $response->assertJsonPath('data.id', $order->id);
    }
    public function test_user_cannot_view_other_user_order()
    {
        $user = User::factory()->create();

        $other = User::factory()->create();

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'user_id' => $other->id,
        ]);

        $this->postJson("/api/order/{$order->id}")
              ->assertForbidden();
    }
    public function test_user_can_cancel_pending_order()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);

        $response = $this->patchJson("/api/order/cancel/{$order->id}");

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Canceled'
        ]);
    }
    public function test_user_cannot_cancel_paid_order()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'Paid'
        ]);

        $this->patchJson("/api/order/cancel/{$order->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Paid'
        ]);
    }
    public function test_admin_can_process_paid_order()
    {
        $admin = User::factory()->admin()->create();

        Sanctum::actingAs($admin);

        $order = Order::factory()->create([
            'status' => 'Paid'
        ]);

        $response = $this->patchJson("/api/admin/order/{$order->id}", [
            'status' => 'Diproses'
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Diproses'
        ]);
    }
    public function test_admin_can_ship_order()
    {
        $admin = User::factory()->admin()->create();

        Sanctum::actingAs($admin);

        $order = Order::factory()->create([
            'status' => 'Diproses'
        ]);

        $response = $this->patchJson("/api/admin/order/{$order->id}", [
            'status' => 'Dikirim',
            'trackingNumber' => 'JNE123456789'
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Dikirim',
            'trackingNumber' => 'JNE123456789'
        ]);
    }
    public function test_admin_cannot_use_duplicate_tracking_number()
    {
        $admin = User::factory()->admin()->create();

        Sanctum::actingAs($admin);

        Order::factory()->create([
            'trackingNumber' => 'JNE123456789'
        ]);

        $order = Order::factory()->create([
            'status' => 'Diproses'
        ]);

        $response = $this->patchJson("/api/admin/order/{$order->id}", [
            'status' => 'Dikirim',
            'trackingNumber' => 'JNE123456789'
        ]);

        $response->assertStatus(400);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Diproses',
            'trackingNumber' => null
        ]);
    }
}
