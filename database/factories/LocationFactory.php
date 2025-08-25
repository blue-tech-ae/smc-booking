<?php

namespace Database\Factories;

use App\Enums\Campus;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company . ' Hall',
            'description' => '<p>' . $this->faker->sentence() . '</p>',
            'campus' => $this->faker->randomElement(array_column(Campus::cases(), 'value')),
        ];
    }
}
