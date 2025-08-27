<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Principal\'s Office',
            'DRC Administration',
            'DSC Administration',
            'SGC Administration',
            'Business Manager',
            'Curriculum & Information Technology',
            'Careers',
            'Future Technologies',
            'Senior Studies',
            'Learning Diversity',
            'Faith and Religious Education',
            'English',
            'Commerce',
            'Humanities',
            'Languages',
            'Performing Arts',
            'Visual Arts',
            'Hospitality',
            'Digital Technologies',
            'Health',
            'Physical Education',
            'Mathematics',
            'Science',
            'Technology & Engineering',
            'Innovative Learning',
            'Acutis',
            'Badano Program',
            'HORIZONS & Accelerated Learning',
            'Inclusive Education',
            'Library Services',
            'Takada Homestay',
            'Logistics Administrator Phillip Di Natale',
            'Pedagogical Coach Amie Panayiotis',
            'Pre & Specialised Testing Coordinator Kyra Farquharson',
            'STEM',
            'Wellbeing',
            'Career Development',
            'Promotions & Events',
            'Alternate Department',
            'Identity, Mission &Community',
            'Professional Practice',
            'Staffing & Administrative Logistics',
            'Co-Curricular Activities & International Perspectives',
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department]);
        }
    }
}

