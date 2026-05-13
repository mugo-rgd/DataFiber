<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('survey_routes', function (Blueprint $table) {
            $table->string('route_code')->unique()->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('survey_routes', function (Blueprint $table) {
            $table->dropColumn('route_code');
        });
    }
};
