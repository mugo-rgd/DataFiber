<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Create migration: php artisan make:migration create_debt_management_role_permissions

// In the migration file:
public function up()
{
    // Create debt management role
    $debtManagerRole = \Spatie\Permission\Models\Role::create([
        'name' => 'debt_manager',
        'guard_name' => 'web'
    ]);

    // Create permissions for debt management
    $permissions = [
        'view_debt_dashboard',
        'view_overdue_invoices',
        'send_payment_reminders',
        'create_payment_plans',
        'approve_payment_extensions',
        'write_off_debt',
        'view_debt_reports',
        'export_debt_data',
        'access_customer_financial_info',
        'update_payment_status',
        'manage_collection_agencies',
        'view_payment_history'
    ];

    foreach ($permissions as $permission) {
        \Spatie\Permission\Models\Permission::create([
            'name' => $permission,
            'guard_name' => 'web'
        ]);
    }

    // Assign all permissions to debt manager role
    $debtManagerRole->givePermissionTo($permissions);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_management_role_permissions');
    }
};
