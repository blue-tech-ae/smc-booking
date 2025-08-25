<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Campus;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'campus_id' => Campus::factory(),
            'description' => '<p>'.$this->faker->sentence.'</p>',
        ];
    }
}

