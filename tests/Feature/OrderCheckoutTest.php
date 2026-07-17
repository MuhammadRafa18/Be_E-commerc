<?php

namespace Tests\Feature;

use App\Models\Addres;
use App\Models\Cart;
use App\Models\Category;
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

class OrderCheckoutTest extends TestCase
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
}
