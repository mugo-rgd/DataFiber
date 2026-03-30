<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SurveyorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a few default surveyors
        $surveyors = [
            [
                'name' => 'John Surveyor',
                'email' => 'john.surveyor@kenyapower.com',
                'password' => Hash::make('password123'),
                'role' => 'surveyor',
                'status' => 'active',
            ],
            [
                'name' => 'Jane Surveyor',
                'email' => 'jane.surveyor@kenyapower.com',
                'password' => Hash::make('password123'),
                'role' => 'surveyor',
                'status' => 'active',
            ],
            [
                'name' => 'Mike Surveyor',
                'email' => 'mike.surveyor@kenyapower.com',
                'password' => Hash::make('password123'),
                'role' => 'surveyor',
                'status' => 'inactive',
            ],
        ];

        foreach ($surveyors as $surveyor) {
            User::updateOrCreate(
                ['email' => $surveyor['email']], // Prevent duplicates
                $surveyor
            );
        }
    }
}



