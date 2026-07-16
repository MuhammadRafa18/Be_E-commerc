<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_super_admin_can_update_other_user()
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin'
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Lama'
        ]);

        $response = $this->actingAs($superAdmin, 'sanctum')
            ->putJson("/api/admin/UserAdmin/{$admin->id}", [
                'name' => 'Admin Baru'
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Baru'
        ]);
    }
    public function test_admin_can_update_himself()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Lama'
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/UserAdmin/{$admin->id}", [
                'name' => 'Admin Baru'
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Baru'
        ]);
    }
    public function test_admin_cannot_update_other_admin()
    {
        $adminLogin = User::factory()->create([
            'role' => 'admin'
        ]);

        $adminTarget = User::factory()->create([
            'role' => 'admin',
            'name' => 'Target'
        ]);

        $response = $this->actingAs($adminLogin, 'sanctum')
            ->putJson("/api/admin/UserAdmin/{$adminTarget->id}", [
                'name' => 'Hacked'
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'id' => $adminTarget->id,
            'name' => 'Hacked'
        ]);
    }
    public function test_super_admin_can_delete_other_user()
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin'
        ]);

        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->actingAs($superAdmin, 'sanctum')
            ->deleteJson("/api/admin/UserAdmin/{$admin->id}");

        $response->assertOk();

        $this->assertDatabaseMissing('users', [
            'id' => $admin->id
        ]);
    }

    public function test_super_admin_cannot_delete_themselves()
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($superAdmin, 'sanctum')
            ->deleteJson("/api/admin/UserAdmin/{$superAdmin->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $superAdmin->id
        ]);
    }

    public function test_admin_cannot_delete_any_user()
    {
        $adminLogin = User::factory()->create([
            'role' => 'admin'
        ]);

        $otherUser = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->actingAs($adminLogin, 'sanctum')
            ->deleteJson("/api/admin/UserAdmin/{$otherUser->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id
        ]);
    }
}
