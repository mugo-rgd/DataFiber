<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Using enum for predefined currencies (better for data integrity)
            $table->enum('currency', ['USD', 'KSH', 'EUR', 'GBP'])->default('USD')->after('total_amount');

            $table->index('currency');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
