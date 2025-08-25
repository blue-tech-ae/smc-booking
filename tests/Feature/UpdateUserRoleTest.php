<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UpdateUserRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->artisan('migrate');
        $this->seed(RolesTableSeeder::class);
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $user = User::factory()->create();
        $user->assignRole('General');

        $roleId = Role::where('name', 'Catering')->first()->id;

        $response = $this->actingAs($admin)->putJson('/api/admin/users/' . $user->id . '/role', [
            'role_id' => $roleId,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($user->fresh()->hasRole('Catering'));
    }

    public function test_super_admin_can_update_user_role(): void
    {
        $super = User::factory()->create();
        $super->assignRole('Super Admin');

        $user = User::factory()->create();
        $user->assignRole('General');

        $roleId = Role::where('name', 'Admin')->first()->id;

        $response = $this->actingAs($super)->putJson('/api/admin/users/' . $user->id . '/role', [
            'role_id' => $roleId,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($user->fresh()->hasRole('Admin'));
    }

    public function test_non_admin_cannot_update_user_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $other = User::factory()->create();
        $other->assignRole('General');

        $roleId = Role::where('name', 'Admin')->first()->id;

        $response = $this->actingAs($user)->putJson('/api/admin/users/' . $other->id . '/role', [
            'role_id' => $roleId,
        ]);

        $response->assertStatus(403);
    }
}
