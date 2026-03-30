<?php

// database/migrations/xxxx_xx_xx_xxxxxx_update_design_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            // Drop old foreign key if it exists
            $table->dropForeign(['surveyor_id']);

            // Now point it to users.id
            $table->foreign('surveyor_id')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            $table->dropForeign(['surveyor_id']);
            $table->foreign('surveyor_id')
                ->references('id')->on('surveyors')
                ->onDelete('set null');
        });
    }
};
