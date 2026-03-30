<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Finance Administrator',
            'company_name' => 'Kenya Power',
            'email' => 'finance@kenyapower.com',
            'phone' => '+254700000001',
            'password' => Hash::make('finance123'),
            'role' => 'finance'
        ]);

        // Create a sample customer user
        // User::create([
        //     'name' => 'Customer Company',
        //     'company_name' => 'Customer Company Ltd',
        //     'email' => 'customer@exam.com',
        //     'phone' => '+254711111111',
        //     'password' => Hash::make('customer123'),
        //     'role' => 'customer'
        // ]);
    }
}
