<?php

namespace Database\Factories;

use App\Models\PhotographyType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotographyTypeFactory extends Factory
{
    protected $model = PhotographyType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
        ];
    }
}
