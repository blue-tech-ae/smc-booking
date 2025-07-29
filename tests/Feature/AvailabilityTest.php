<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventService;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityTest extends TestCase
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

    public function test_it_returns_available_locations_and_photography_status(): void
    {
        $loc1 = Location::factory()->create();
        $loc2 = Location::factory()->create();

        $user = User::factory()->create();
        $user->assignRole('General');

        $photographer = User::factory()->create();
        $photographer->assignRole('Photography');

        $event = Event::factory()->for($user)->for($loc1)->create([
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
        ]);

        EventService::create([
            'event_id' => $event->id,
            'service_type' => 'photography',
            'assigned_to' => $photographer->id,
            'details' => [],
        ]);

        $response = $this->getJson('/api/availability?start_time=' . now()->addDay()->toDateTimeString() . '&end_time=' . now()->addDays(2)->toDateTimeString());

        $response->assertStatus(200);
        $response->assertJsonPath('data.photography_available', false);
        $response->assertJsonCount(1, 'data.locations');
        $response->assertEquals($loc2->id, $response->json('data.locations.0.id'));
    }
}
