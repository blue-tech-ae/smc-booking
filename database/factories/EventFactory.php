<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'location_id' => Location::factory(),
            'title' => $this->faker->sentence,
            'details' => $this->faker->paragraph,
            'expected_attendance' => $this->faker->numberBetween(10, 100),
            'organizer_name' => $this->faker->name,
            'organizer_email' => $this->faker->safeEmail,
            'organizer_phone' => $this->faker->phoneNumber,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
            'status' => 'pending',
        ];
    }
}

