<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Davisson Street Campus',
            'Dalton Road Campus',
            'SGC Campus',
        ];

        foreach ($names as $name) {
            Campus::firstOrCreate(['name' => $name]);
        }
    }
}
