<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Example permissions (you can add more as needed)
     Permission::firstOrCreate(['name' => 'manage customers', 'guard_name' => 'web']);
Permission::firstOrCreate(['name' => 'view contracts', 'guard_name' => 'web']);
Permission::firstOrCreate(['name' => 'manage billing', 'guard_name' => 'web']);

$adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
$customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Give permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        $customerRole->givePermissionTo(['view contracts']);
    }
}

