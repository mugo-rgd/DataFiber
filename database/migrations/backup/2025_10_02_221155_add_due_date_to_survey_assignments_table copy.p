<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('survey_assignments', function (Blueprint $table) {
            $table->dateTime('due_date')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('survey_assignments', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
