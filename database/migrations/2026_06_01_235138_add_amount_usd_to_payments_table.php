<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount_usd', 15, 2)->nullable()->after('amount_kes');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount_kes', 15, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('amount_usd');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount_kes', 15, 2)->nullable(false)->change();
        });
    }
};
