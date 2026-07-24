<?php

namespace Tests\Feature;

use App\Models\Addres;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_only_their_addresses(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $address = Addres::factory()->create(['user_id' => $user->id]);
        Addres::factory()->create(['user_id' => $otherUser->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/addres');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $address->id);
    }

    public function test_user_can_create_address(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/addres', $this->addressPayload());

        $response->assertCreated();
        $this->assertDatabaseHas('addres', [
            'user_id' => $user->id,
            'fullname' => 'Nazla Putri',
            'city' => 'Bandung',
        ]);
    }

    public function test_user_cannot_create_more_than_three_addresses(): void
    {
        $user = User::factory()->create();
        Addres::factory()->count(3)->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/addres', $this->addressPayload());

        $response->assertStatus(422)
            ->assertJsonPath('errors.address.0', 'Maksimal 3 alamat');
        $this->assertDatabaseCount('addres', 3);
    }

    public function test_user_can_view_their_address_detail(): void
    {
        $user = User::factory()->create();
        $address = Addres::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/addres/{$address->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $address->id);
    }

    public function test_user_cannot_view_other_user_address(): void
    {
        $user = User::factory()->create();
        $otherAddress = Addres::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/addres/{$otherAddress->id}");

        $response->assertNotFound();
    }

    public function test_user_can_update_their_address(): void
    {
        $user = User::factory()->create();
        $address = Addres::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/addres/{$address->id}", [
            'city' => 'Jakarta',
            'place' => 'Kantor',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('addres', [
            'id' => $address->id,
            'city' => 'Jakarta',
            'place' => 'Kantor',
        ]);
    }

    public function test_user_cannot_update_other_user_address(): void
    {
        $user = User::factory()->create();
        $otherAddress = Addres::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/addres/{$otherAddress->id}", [
            'city' => 'Jakarta',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseMissing('addres', [
            'id' => $otherAddress->id,
            'city' => 'Jakarta',
        ]);
    }

    public function test_user_can_delete_their_address(): void
    {
        $user = User::factory()->create();
        $address = Addres::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/addres/{$address->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('addres', [
            'id' => $address->id,
        ]);
    }

    private function addressPayload(): array
    {
        return [
            'fullname' => 'Nazla Putri',
            'streetname' => 'Jl. Merdeka No. 10',
            'place' => 'Rumah',
            'provinci' => 'Jawa Barat',
            'city' => 'Bandung',
        ];
    }
}
