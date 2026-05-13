<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('colocation_services', function (Blueprint $table) {
            $table->decimal('service_area', 8, 2)->nullable()->after('rack_units');
        });
    }

    public function down(): void
    {
        Schema::table('colocation_services', function (Blueprint $table) {
            $table->dropColumn('service_area');
        });
    }
};
