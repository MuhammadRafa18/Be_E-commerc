<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductFashion;
use App\Models\ProductSku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_product_list(): void
    {
        $product = $this->createFashionProduct();

        $response = $this->getJson('/api/product');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $product->id);
    }

    public function test_guest_can_view_product_detail(): void
    {
        $product = $this->createFashionProduct();

        $response = $this->postJson("/api/product/{$product->slug}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_regular_user_cannot_create_product(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->create());
        $category = Category::factory()->fashion()->create();

        $response = $this->postJson('/api/admin/product', $this->fashionPayload($category));

        $response->assertForbidden();
        $this->assertDatabaseMissing('product', [
            'title' => 'Essential Shirt',
        ]);
    }

    public function test_admin_can_create_fashion_product(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->admin()->create());
        $category = Category::factory()->fashion()->create();

        $response = $this->postJson('/api/admin/product', $this->fashionPayload($category));

        $response->assertCreated();
        $this->assertDatabaseHas('product', [
            'title' => 'Essential Shirt',
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('product_sku', [
            'price' => 150000,
            'sell_price' => 120000,
            'stock' => 8,
        ]);
        $this->assertDatabaseHas('product_fashion', [
            'size' => 'M',
            'color' => 'Black',
        ]);
    }

    public function test_admin_cannot_create_product_with_invalid_payload(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->admin()->create());
        $category = Category::factory()->fashion()->create();

        $payload = $this->fashionPayload($category);
        unset($payload['variants']);

        $response = $this->postJson('/api/admin/product', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('variants');
    }

    public function test_regular_user_cannot_update_product(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $product = $this->createFashionProduct();

        $response = $this->patchJson("/api/admin/product/{$product->id}", [
            'title' => 'Updated Product',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('product', [
            'id' => $product->id,
            'title' => $product->title,
        ]);
    }

    public function test_admin_can_update_product(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $product = $this->createFashionProduct();

        $response = $this->patchJson("/api/admin/product/{$product->id}", [
            'title' => 'Updated Product',
            'price' => 175000,
            'sell_price' => 130000,
            'stock' => 12,
            'weight_gram' => 250,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('product', [
            'id' => $product->id,
            'title' => 'Updated Product',
        ]);
        $this->assertDatabaseHas('product_sku', [
            'product_id' => $product->id,
            'sell_price' => 130000,
            'stock' => 12,
        ]);
    }

    public function test_admin_can_delete_unused_product(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $product = $this->createFashionProduct();

        $response = $this->deleteJson("/api/admin/product/{$product->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('product', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_cannot_delete_product_used_in_order(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $product = $this->createFashionProduct();

        DB::table('order_items')->insert([
            'order_id' => Order::factory()->create()->id,
            'product_id' => $product->id,
            'product_sku_id' => $product->product_sku()->first()->id,
            'product_title' => $product->title,
            'product_image' => $product->image_produk,
            'product_size' => 'M',
            'produk_sell_price' => 120000,
            'qty' => 1,
            'subtotal' => 120000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->deleteJson("/api/admin/product/{$product->id}");

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Produk tidak bisa dihapus karena sudah ada di order');
        $this->assertDatabaseHas('product', [
            'id' => $product->id,
        ]);
    }

    private function fashionPayload(Category $category): array
    {
        return [
            'image_produk' => UploadedFile::fake()->image('product.jpg'),
            'image_banner' => UploadedFile::fake()->image('banner.jpg'),
            'title' => 'Essential Shirt',
            'category_id' => $category->id,
            'description' => 'Comfortable daily shirt.',
            'price' => 150000,
            'sell_price' => 120000,
            'stock' => 8,
            'weight_gram' => 200,
            'variants' => [
                [
                    'size' => 'M',
                    'color' => 'Black',
                ],
            ],
        ];
    }

    private function createFashionProduct(): Product
    {
        $category = Category::factory()->fashion()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $sku = ProductSku::factory()->create([
            'product_id' => $product->id,
        ]);
        ProductFashion::factory()->create([
            'product_sku_id' => $sku->id,
        ]);

        return $product;
    }
}
