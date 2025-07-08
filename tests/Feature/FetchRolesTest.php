<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class FetchRolesTest extends TestCase
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

    public function test_admin_can_fetch_roles(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->getJson('/api/admin/roles');

        $response->assertStatus(200);
        $response->assertJsonCount(Role::count(), 'data');
    }
}
