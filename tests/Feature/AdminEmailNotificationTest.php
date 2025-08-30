<?php

namespace Tests\Feature;

use App\Mail\EventApprovalRequest;
use App\Models\Location;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEmailNotificationTest extends TestCase
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

    public function test_admin_gets_email_after_event_creation(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $user->assignRole('General');

        $admin = User::factory()->create(['email' => 'admin@test.com']);
        $admin->assignRole('Admin');

        $location = Location::factory()->create();
        $department = Department::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Email Test',
            'location' => $location->name,
            'start_time' => now()->addDays(15)->toDateTimeString(),
            'end_time' => now()->addDays(16)->toDateTimeString(),
            'expected_attendance' => 20,
            'department' => $department->name,
            'campus' => $location->campus->value,
        ]);

        $response->assertStatus(201);

        Mail::assertSent(EventApprovalRequest::class, function ($mail) use ($admin) {
            return in_array($admin->email, array_keys($mail->to));
        });
    }
}
