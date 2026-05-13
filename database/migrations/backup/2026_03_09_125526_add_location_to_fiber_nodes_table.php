<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fiber_nodes', function (Blueprint $table) {

            $table->geometry('location')->nullable();

        });
    }

    public function down()
    {
        Schema::table('fiber_nodes', function (Blueprint $table) {

            $table->dropColumn('location');

        });
    }
};
