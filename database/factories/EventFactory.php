<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Department;
use App\Enums\Campus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $start = Carbon::instance($this->faker->dateTimeBetween('+1 days', '+2 days'));
        $end = (clone $start)->addHours(2);

        return [
            'user_id' => User::factory(),
            'location' => $this->faker->city,
            'department' => Department::factory()->create()->name,
            'campus' => $this->faker->randomElement(array_column(Campus::cases(), 'value')),
            'title' => $this->faker->sentence,
            'details' => $this->faker->paragraph,
            'expected_attendance' => $this->faker->numberBetween(1, 100),
            'organizer_name' => $this->faker->name,
            'organizer_email' => $this->faker->safeEmail,
            'organizer_phone' => $this->faker->phoneNumber,
            'start_time' => $start,
            'end_time' => $end,
            'security_note' => $this->faker->sentence,
            'setup_details' => [],
            'gift_details' => [],
            'floral_details' => [],
            'status' => 'pending',
        ];
    }
}

