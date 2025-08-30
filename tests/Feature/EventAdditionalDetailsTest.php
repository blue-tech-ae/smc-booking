<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Location;
use App\Models\Department;
use App\Models\Event;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class EventAdditionalDetailsTest extends TestCase
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

    public function test_event_can_store_additional_details(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');
        $location = Location::factory()->create();
        $department = Department::factory()->create();
        $start = Carbon::now()->addDays(10);
        $end = (clone $start)->addHours(2);

        $payload = [
            'title' => 'Test Event',
            'location' => $location->name,
            'department' => $department->name,
            'campus' => $location->campus->value,
            'start_time' => $start->toDateTimeString(),
            'end_time' => $end->toDateTimeString(),
            'security_note' => 'Please ensure security.',
            'setup_details' => [
                'av_technician' => true,
                'av_equipment' => ['projector', 'microphones'],
                'chairs' => 50,
                'tables' => 10,
                'table_type' => 'round',
            ],
            'gift_details' => [
                'required' => true,
                'quantity' => 20,
                'delivery_location' => 'Main hall',
                'type' => 'Branded mugs',
            ],
            'floral_details' => [
                'required' => true,
                'delivery_time' => $start->copy()->subDay()->toDateTimeString(),
                'amount' => 200,
                'theme' => 'Roses at entrance',
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/events', $payload);
        $response->assertStatus(201);

        $event = Event::first();
        $this->assertTrue($event->setup_details['av_technician']);
        $this->assertEquals('round', $event->setup_details['table_type']);
        $this->assertEquals(20, $event->gift_details['quantity']);
        $this->assertEquals('Roses at entrance', $event->floral_details['theme']);
    }

    public function test_event_can_update_additional_details(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');
        $location = Location::factory()->create();
        $department = Department::factory()->create();
        $start = Carbon::now()->addWeeks(3);
        $end = (clone $start)->addHours(2);

        $event = Event::factory()->for($user)->create([
            'location' => $location->name,
            'department' => $department->name,
            'campus' => $location->campus->value,
            'start_time' => $start,
            'end_time' => $end,
        ]);

        $payload = [
            'gift_details' => [
                'required' => true,
                'quantity' => 10,
                'delivery_location' => 'Reception',
                'type' => 'Books',
            ],
            'floral_details' => [
                'required' => false,
            ],
        ];

        $response = $this->actingAs($user)->putJson('/api/events/' . $event->id, $payload);
        $response->assertStatus(200);

        $event->refresh();
        $this->assertEquals('Books', $event->gift_details['type']);
        $this->assertFalse($event->floral_details['required']);
    }
}
