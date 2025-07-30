<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Location;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityServiceTest extends TestCase
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

    public function test_event_creation_allows_missing_security_guards(): void
    {
        $user = User::factory()->create();
        $user->assignRole('General');

        $location = Location::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Security Event',
            'location_id' => $location->id,
            'start_time' => now()->addDay()->toDateTimeString(),
            'end_time' => now()->addDays(2)->toDateTimeString(),
            'services' => [
                [
                    'service_type' => 'security',
                    'details' => [
                        'required' => true,
                    ],
                ],
            ],
        ]);

        $response->assertStatus(201);
    }
}
