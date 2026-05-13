<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreignId('survey_route_id')->nullable()->after('id')
                  ->constrained('survey_routes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['survey_route_id']);
            $table->dropColumn('survey_route_id');
        });
    }
};
