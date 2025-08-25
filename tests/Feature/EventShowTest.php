<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventShowTest extends TestCase
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

    public function test_user_can_view_own_event(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $event = Event::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/events/' . $event->id);

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $event->id);
    }

    public function test_admin_can_view_any_event(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $event = Event::factory()->create();

        $response = $this->actingAs($admin)->getJson('/api/events/' . $event->id);

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $event->id);
    }

    public function test_user_cannot_view_others_event(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $otherUser = User::factory()->create();
        $otherUser->assignRole('General');
        $event = Event::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->getJson('/api/events/' . $event->id);

        $response->assertStatus(403);
    }
}

