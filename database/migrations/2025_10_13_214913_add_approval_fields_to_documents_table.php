<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

   public function up()
{
    Schema::table('documents', function (Blueprint $table) {
        // $table->string('status')->default('pending'); // pending, approved, rejected
        $table->foreignId('approved_by')->nullable()->constrained('users');
        $table->timestamp('approved_at')->nullable();
        $table->text('rejection_reason')->nullable();
    });
}

public function down()
{
    Schema::table('documents', function (Blueprint $table) {
        $table->dropColumn(['status', 'approved_by', 'approved_at', 'rejection_reason']);
    });
}
};
