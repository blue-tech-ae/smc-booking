<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
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

    public function test_user_can_set_new_password_if_none_exists(): void
    {
        $user = User::factory()->withoutPassword()->create();
        $user->assignRole('General');

        $response = $this->actingAs($user)->putJson('/api/user/password', [
            'password' => 'newsecret',
            'password_confirmation' => 'newsecret',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('newsecret', $user->fresh()->password));
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create(); // default password is 'password'
        $user->assignRole('General');

        $response = $this->actingAs($user)->putJson('/api/user/password', [
            'current_password' => 'password',
            'password' => 'changedpass',
            'password_confirmation' => 'changedpass',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('changedpass', $user->fresh()->password));
    }

    public function test_change_fails_with_incorrect_current_password(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $response = $this->actingAs($user)->putJson('/api/user/password', [
            'current_password' => 'wrongpass',
            'password' => 'newpass',
            'password_confirmation' => 'newpass',
        ]);

        $response->assertStatus(422);
    }
}
