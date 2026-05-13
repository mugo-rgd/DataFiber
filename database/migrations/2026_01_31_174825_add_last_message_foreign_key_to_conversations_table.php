<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wait a moment to ensure messages table exists
        // This migration should run AFTER create_messages_table

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('last_message_id')
                  ->references('id')
                  ->on('messages')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['last_message_id']);
        });
    }
};
