<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('custom_routes', function (Blueprint $table) {
        $table->integer('contract_duration_months')->default(12)->after('availability');
    });
}

public function down(): void
{
    Schema::table('custom_routes', function (Blueprint $table) {
        $table->dropColumn('contract_duration_months');
    });
}
};
