<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventUpdateTest extends TestCase
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

    public function test_admin_can_update_any_event(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('General');
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $location = Location::factory()->create();
        $event = Event::factory()->for($owner)->for($location)->create([
            'title' => 'Original',
            'start_time' => now()->addDays(5),
            'end_time' => now()->addDays(6),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->putJson('/api/admin/events/' . $event->id, [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Title', $event->fresh()->title);
        $this->assertEquals('approved', $event->fresh()->status);
    }

    public function test_non_admin_cannot_use_admin_update(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');
        $location = Location::factory()->create();
        $event = Event::factory()->for($user)->for($location)->create([
            'start_time' => now()->addDays(5),
            'end_time' => now()->addDays(6),
        ]);

        $response = $this->actingAs($user)->putJson('/api/admin/events/' . $event->id, [
            'title' => 'Fail Update',
        ]);

        $response->assertStatus(403);
    }
}
