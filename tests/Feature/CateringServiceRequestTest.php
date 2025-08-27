<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventService;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CateringServiceRequestTest extends TestCase
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

    public function test_catering_service_requires_people_field(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');
        $location = Location::factory()->create();
        $event = Event::factory()->for($user)->for($location)->create();

        $data = [
            'service_type' => 'catering',
            'details' => [
                'external_guests' => true,
                'service_time' => '10:00',
                'food_types' => ['Sandwiches'],
                'coffee_station' => false,
            ],
        ];

        $response = $this->actingAs($user)->postJson("/api/events/{$event->id}/services", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['details.people']);
    }

    public function test_catering_service_accepts_full_details(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');
        $location = Location::factory()->create();
        $event = Event::factory()->for($user)->for($location)->create();

        $data = [
            'service_type' => 'catering',
            'details' => [
                'external_guests' => true,
                'people' => 50,
                'service_time' => '12:30',
                'food_types' => ['Sandwiches', 'Other'],
                'coffee_station' => true,
                'beverages' => ['Bottled Water', 'Juices'],
                'dietary_requirements' => [
                    ['type' => 'Vegan', 'count' => 5],
                    ['type' => 'Other', 'count' => 2, 'note' => 'Halal'],
                ],
                'extra_notes' => 'No pork',
            ],
        ];

        $response = $this->actingAs($user)->postJson("/api/events/{$event->id}/services", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('event_services', [
            'event_id' => $event->id,
            'service_type' => 'catering',
        ]);
        $service = EventService::where('event_id', $event->id)
            ->where('service_type', 'catering')
            ->first();
        $this->assertEquals(50, $service->details['people']);
        $this->assertEquals('No pork', $service->details['extra_notes']);
    }
}
