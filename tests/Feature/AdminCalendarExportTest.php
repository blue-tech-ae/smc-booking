<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCalendarExportTest extends TestCase
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

    public function test_admin_can_export_filtered_events(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $location = Location::factory()->create();

        Event::factory()->for($admin)->for($location)->create([
            'title' => 'Exportable Event',
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(2),
        ]);

        $response = $this->actingAs($admin)->get('/api/admin/calendar-view/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv');
        $this->assertStringContainsString('Exportable Event', $response->streamedContent());
    }
}
