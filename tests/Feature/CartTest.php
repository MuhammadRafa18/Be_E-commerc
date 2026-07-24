<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFashion;
use App\Models\ProductSku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_only_their_cart_items(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $variant = $this->createFashionVariant();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $variant['product']->id,
            'product_sku_id' => $variant['sku']->id,
            'product_fashion_id' => $variant['fashion']->id,
        ]);
        Cart::factory()->create(['user_id' => $otherUser->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/cart');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $cart->id);
    }

    public function test_user_can_add_fashion_product_to_cart(): void
    {
        $user = User::factory()->create();
        $variant = $this->createFashionVariant(['stock' => 10]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/cart', $this->cartPayload($variant, 2));

        $response->assertCreated();
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $variant['product']->id,
            'product_sku_id' => $variant['sku']->id,
            'product_fashion_id' => $variant['fashion']->id,
            'qty' => 2,
        ]);
    }

    public function test_user_adding_same_variant_increases_cart_quantity(): void
    {
        $user = User::factory()->create();
        $variant = $this->createFashionVariant(['stock' => 10]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $variant['product']->id,
            'product_sku_id' => $variant['sku']->id,
            'product_fashion_id' => $variant['fashion']->id,
            'qty' => 2,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/cart', $this->cartPayload($variant, 3));

        $response->assertCreated();
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_sku_id' => $variant['sku']->id,
            'product_fashion_id' => $variant['fashion']->id,
            'qty' => 5,
        ]);
    }

    public function test_user_cannot_add_quantity_more_than_stock(): void
    {
        $user = User::factory()->create();
        $variant = $this->createFashionVariant(['stock' => 1]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/cart', $this->cartPayload($variant, 5));

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Gagal: Total di keranjangmu (5 pcs) melebihi stok gudang (1 pcs).');
        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_sku_id' => $variant['sku']->id,
            'qty' => 5,
        ]);
    }

    public function test_user_cannot_add_variant_from_different_product(): void
    {
        $user = User::factory()->create();
        $variant = $this->createFashionVariant();
        $otherVariant = $this->createFashionVariant();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/cart', [
            'product_id' => $variant['product']->id,
            'product_sku_id' => $variant['sku']->id,
            'product_fashion_id' => $otherVariant['fashion']->id,
            'qty' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Variant fashion tidak valid');
    }

    public function test_user_can_toggle_selected_cart_item(): void
    {
        $user = User::factory()->create();
        $variant = $this->createFashionVariant();
        $cart = Cart::factory()->selected()->create([
            'user_id' => $user->id,
            'product_id' => $variant['product']->id,
            'product_sku_id' => $variant['sku']->id,
            'product_fashion_id' => $variant['fashion']->id,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/cart/selected/{$cart->id}");

        $response->assertOk()
            ->assertJsonPath('is_selected', false);
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'is_selected' => false,
        ]);
    }

    public function test_user_cannot_toggle_other_user_cart_item(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->selected()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/cart/selected/{$cart->id}");

        $response->assertNotFound();
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'is_selected' => true,
        ]);
    }

    public function test_user_can_delete_their_cart_item(): void
    {
        $user = User::factory()->create();
        $variant = $this->createFashionVariant();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $variant['product']->id,
            'product_sku_id' => $variant['sku']->id,
            'product_fashion_id' => $variant['fashion']->id,
        ]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/cart/delete/{$cart->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('carts', [
            'id' => $cart->id,
        ]);
    }

    private function cartPayload(array $variant, int $qty = 1): array
    {
        return [
            'product_id' => $variant['product']->id,
            'product_sku_id' => $variant['sku']->id,
            'product_fashion_id' => $variant['fashion']->id,
            'qty' => $qty,
        ];
    }

    private function createFashionVariant(array $skuAttributes = []): array
    {
        $category = Category::factory()->fashion()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $sku = ProductSku::factory()->create(array_merge([
            'product_id' => $product->id,
            'stock' => 10,
        ], $skuAttributes));
        $fashion = ProductFashion::factory()->create([
            'product_sku_id' => $sku->id,
        ]);

        return compact('product', 'sku', 'fashion');
    }
}
