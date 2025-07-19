<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventLeadTimeTest extends TestCase
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

    public function test_event_fails_without_required_lead_time(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Test Event',
            'location_id' => $location->id,
            'start_time' => now()->addWeeks(3)->toDateTimeString(),
            'end_time' => now()->addWeeks(3)->addDay()->toDateTimeString(),
            'expected_attendance' => 40,
        ]);

        $response->assertStatus(422);
    }

    public function test_event_succeeds_with_sufficient_lead_time(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Allowed Event',
            'location_id' => $location->id,
            'start_time' => now()->addWeeks(5)->toDateTimeString(),
            'end_time' => now()->addWeeks(5)->addDay()->toDateTimeString(),
            'expected_attendance' => 40,
        ]);

        $response->assertStatus(201);
    }
}
