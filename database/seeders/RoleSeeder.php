<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view quotations',
            'create quotations',
            'edit quotations',
            'delete quotations',
            'approve quotations',
            'reject quotations',
            'send quotations',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        $system_admin = Role::create(['name' => 'system_admin', 'guard_name' => 'web']);
        $system_admin->givePermissionTo(Permission::all());

        $accountmanager_admin = Role::create(['name' => 'accountmanager_admin', 'guard_name' => 'web']);
        $accountmanager_admin->givePermissionTo(Permission::all());

        $account_manager = Role::create(['name' => 'account_manager', 'guard_name' => 'web']);
        $account_manager->givePermissionTo([
            'view quotations',
            'create quotations',
            'edit quotations',
            'send quotations'
        ]);
    }
}
