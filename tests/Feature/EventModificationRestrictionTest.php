<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Location;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventModificationRestrictionTest extends TestCase
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

    public function test_user_cannot_update_event_within_two_weeks(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $event = Event::factory()->for($user)->for($location)->create([
            'start_time' => now()->addDays(10),
            'end_time' => now()->addDays(11),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->putJson('/api/events/' . $event->id, [
            'title' => 'Updated',
        ]);

        $response->assertStatus(403);
    }

    public function test_updating_approved_event_sets_status_to_pending(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $event = Event::factory()->for($user)->for($location)->create([
            'start_time' => now()->addDays(30),
            'end_time' => now()->addDays(31),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->putJson('/api/events/' . $event->id, [
            'title' => 'Updated',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('pending', $event->fresh()->status);
    }

    public function test_user_cannot_cancel_event_within_two_weeks(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $event = Event::factory()->for($user)->for($location)->create([
            'start_time' => now()->addDays(10),
            'end_time' => now()->addDays(11),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->postJson('/api/events/' . $event->id . '/cancel', [
            'reason' => 'Change of plans',
        ]);

        $response->assertStatus(403);
    }

    public function test_cancelling_event_creates_request_and_sets_pending(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $event = Event::factory()->for($user)->for($location)->create([
            'start_time' => now()->addDays(30),
            'end_time' => now()->addDays(31),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->postJson('/api/events/' . $event->id . '/cancel', [
            'reason' => 'Change of plans',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('pending', $event->fresh()->status);
        $this->assertDatabaseHas('cancellation_requests', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'reason' => 'Change of plans',
        ]);
    }
}
