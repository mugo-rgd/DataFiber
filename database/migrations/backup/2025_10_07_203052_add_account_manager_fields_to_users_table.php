<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('account_manager_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->text('assignment_notes')->nullable();
            // $table->string('role')->default('customer'); // Ensure role column exists
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['account_manager_id']);
            $table->dropColumn(['account_manager_id', 'assigned_at', 'assignment_notes']);
        });
    }
};
