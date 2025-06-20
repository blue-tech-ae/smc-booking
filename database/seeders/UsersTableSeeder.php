<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'super@demo.com',
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@demo.com',
                'role' => 'Admin',
            ],
            [
                'name' => 'General User',
                'email' => 'general@demo.com',
                'role' => 'General',
            ],
                        [
                'name' => 'General User2',
                'email' => 'general2@demo.com',
                'role' => 'General',
            ],
                        [
                'name' => 'General User3',
                'email' => 'general3@demo.com',
                'role' => 'General',
            ],
                        [
                'name' => 'General User4',
                'email' => 'general4@demo.com',
                'role' => 'General',
            ],
            [
                'name' => 'Catering Staff',
                'email' => 'catering@demo.com',
                'role' => 'Catering',
            ],
            [
                'name' => 'Photographer',
                'email' => 'photo@demo.com',
                'role' => 'Photography',
            ],
            [
                'name' => 'Security Staff',
                'email' => 'security@demo.com',
                'role' => 'Security',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
