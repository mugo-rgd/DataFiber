<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TechnicianUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a few default surveyors
        $technicians = [
            [
                'name' => 'John K Technician',
                'email' => 'john.technician@darkfibre.com',
                'password' => Hash::make('password123'),
                'role' => 'technician',
                'status' => 'active',
            ],
         
        ];

        foreach ($technicians as $technician) {
            User::updateOrCreate(
                ['email' => $technician['email']], // Prevent duplicates
                $technician
            );
        }
    }
}



