<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventService;
use App\Models\Location;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingsRoleFilterTest extends TestCase
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

    public function test_admin_can_filter_bookings_by_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $owner = User::factory()->create();
        $owner->assignRole('General');

        $location = Location::factory()->create();
        $department = Department::factory()->create();

        $photoEvent = Event::create([
            'user_id' => $owner->id,
            'location_id' => $location->id,
            'department' => $department->name,
            'campus' => $location->campus->value,
            'title' => 'Photo Event',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
            'status' => 'pending',
        ]);
        EventService::create([
            'event_id' => $photoEvent->id,
            'service_type' => 'photography',
        ]);

        $cateringEvent = Event::create([
            'user_id' => $owner->id,
            'location_id' => $location->id,
            'department' => $department->name,
            'campus' => $location->campus->value,
            'title' => 'Catering Event',
            'start_time' => now()->addDays(3),
            'end_time' => now()->addDays(4),
            'status' => 'pending',
        ]);
        EventService::create([
            'event_id' => $cateringEvent->id,
            'service_type' => 'catering',
        ]);

        $response = $this->actingAs($admin)->getJson('/api/admin/bookings?role=photography');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Photo Event', $response->json('data.0.title'));
    }
}

