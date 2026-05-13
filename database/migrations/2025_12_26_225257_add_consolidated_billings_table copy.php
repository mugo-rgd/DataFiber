<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
Schema::table('consolidated_billings', function (Blueprint $table) {
    $table->decimal('exchange_rate', 10, 6)->nullable()->after('currency');
    $table->decimal('total_amount_kes', 12, 2)->nullable()->after('total_amount');
});
    }

    // public function down()
    // {
    //     Schema::dropIfExists('consolidated_billings');
    // }database\migrations\2025_12_26_225256_add_consolidated_billings_table copy.php
};
