<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Location;
use App\Models\PhotographyType;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotographyTypeTest extends TestCase
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

    public function test_admin_can_create_photography_type(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->postJson('/api/admin/photography-types', [
            'name' => 'Wedding',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('photography_types', ['name' => 'Wedding']);
    }

    public function test_event_creation_requires_valid_photography_type_id(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Test',
            'location_id' => $location->id,
            'start_time' => now()->addDay()->toDateTimeString(),
            'end_time' => now()->addDays(2)->toDateTimeString(),
            'services' => [
                [
                    'service_type' => 'photography',
                    'details' => [
                        'required' => true,
                        'photography_type_id' => 999,
                    ],
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_event_creation_allows_missing_photography_type_id(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Test',
            'location_id' => $location->id,
            'start_time' => now()->addDay()->toDateTimeString(),
            'end_time' => now()->addDays(2)->toDateTimeString(),
            'services' => [
                [
                    'service_type' => 'photography',
                    'details' => [
                        'required' => true,
                    ],
                ],
            ],
        ]);

        $response->assertStatus(201);
    }

    public function test_can_fetch_photography_types(): void
    {
        PhotographyType::factory()->count(3)->create();

        $response = $this->getJson('/api/photography-types');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }
}
