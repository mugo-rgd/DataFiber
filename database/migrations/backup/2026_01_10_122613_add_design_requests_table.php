<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
   Schema::table('design_requests', function (Blueprint $table) {
    $table->foreignId('ict_engineer_id')->nullable()->constrained('users');
    $table->timestamp('assigned_to_ict_at')->nullable();
    $table->string('ict_status')->nullable()->default('pending'); // pending, assigned, completed
});
}

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::table('documents', function (Blueprint $table) {
    //         //
    //     });
    // }
};
