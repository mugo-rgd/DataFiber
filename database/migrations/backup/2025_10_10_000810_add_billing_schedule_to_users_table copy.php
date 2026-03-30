<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('lease_start_date')->nullable()->after('email');
            $table->enum('billing_frequency', ['monthly', 'quarterly', 'annually'])->default('monthly')->after('lease_start_date');
            $table->decimal('monthly_rate', 10, 2)->default(0)->after('billing_frequency');
            $table->date('next_billing_date')->nullable()->after('monthly_rate');
            $table->boolean('auto_billing_enabled')->default(true)->after('next_billing_date');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'lease_start_date',
                'billing_frequency',
                'monthly_rate',
                'next_billing_date',
                'auto_billing_enabled'
            ]);
        });
    }
};
