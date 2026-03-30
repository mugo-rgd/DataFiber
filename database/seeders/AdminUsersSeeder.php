<?php
// database/seeders/AdminUsersSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUsersSeeder extends Seeder
{
    public function run()
    {
        // User::create([
        //     'name' => 'System Administrator',
        //     'email' => 'system.admin@darkfibre.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'system_admin',
        //     'status' => 'active',
        // ]);

        // User::create([
        //     'name' => 'Marketing Administrator',
        //     'email' => 'marketing.admin@darkfibre.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'accountmanager_admin',
        //     'status' => 'active',
        // ]);

        User::create([
            'name' => 'Technical Administrator',
            'email' => 'technical.admin@darkfibre.com',
            'password' => Hash::make('password'),
            'role' => 'technical_admin',
            'status' => 'active',
        ]);
    }
}
