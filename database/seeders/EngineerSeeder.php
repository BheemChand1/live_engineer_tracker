<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Engineer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EngineerSeeder extends Seeder
{
    public function run(): void
    {
        // Create engineer users with profiles
        $engineers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@computerrepair.com',
                'phone' => '+1234567890',
                'skills' => ['Hardware Repair', 'Software Installation', 'Network Setup'],
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@computerrepair.com',
                'phone' => '+1234567891',
                'skills' => ['Laptop Repair', 'Data Recovery', 'Virus Removal'],
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@computerrepair.com',
                'phone' => '+1234567892',
                'skills' => ['Hardware Diagnosis', 'Component Replacement', 'Performance Optimization'],
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@computerrepair.com',
                'phone' => '+1234567893',
                'skills' => ['Software Troubleshooting', 'System Installation', 'Mobile Device Repair'],
            ],
            [
                'name' => 'Robert Brown',
                'email' => 'robert.brown@computerrepair.com',
                'phone' => '+1234567894',
                'skills' => ['Network Security', 'Server Maintenance', 'Cloud Setup'],
            ],
        ];

        foreach ($engineers as $engineerData) {
            // Create user account
            $user = User::create([
                'name' => $engineerData['name'],
                'email' => $engineerData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('engineer123'),
                'role' => 'engineer',
            ]);

            // Create engineer profile
            Engineer::create([
                'name' => $engineerData['name'],
                'email' => $engineerData['email'],
                'phone' => $engineerData['phone'],
                'skills' => json_encode($engineerData['skills']),
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }
    }
}
