<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_favorite_list(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $product = Product::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/favorite');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'product'
                    ]
                ]
            ]);
    }

    public function test_user_only_sees_his_own_favorites(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $otherUser = User::factory()->create([
            'role' => 'user'
        ]);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $product1->id,
        ]);

        Favorite::create([
            'user_id' => $otherUser->id,
            'product_id' => $product2->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/favorite');

        $response->assertOk();

        $this->assertCount(1, $response->json('data'));

        $this->assertEquals(
            $product1->id,
            $response->json('data.0.product.id')
        );
    }

    public function test_user_gets_404_when_no_favorites_exist(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/favorite');

        $response->assertStatus(404)
            ->assertJson([
                'messages' => 'Favorite  not found'
            ]);
    }

    public function test_user_can_like_product(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/favorite', [
            'product_id' => $product->id
        ]);

        $response->assertCreated()
            ->assertJson([
                'status' => 'liked'
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_unlike_product(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $product = Product::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/favorite', [
            'product_id' => $product->id
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'unliked'
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_cannot_like_non_existing_product(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/favorite', [
            'product_id' => 999999
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('product_id');
    }

    public function test_guest_cannot_access_favorite(): void
    {
        $this->getJson('/api/favorite')
            ->assertUnauthorized();

        $this->postJson('/api/favorite', [
            'product_id' => 1
        ])->assertUnauthorized();
    }
}
