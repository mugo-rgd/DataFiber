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
    Schema::table('messages', function (Blueprint $table) {
        $table->string('type')->default('text')->after('body');
        $table->string('file_name')->nullable()->after('type');
        $table->string('file_path')->nullable()->after('file_name');
        $table->string('file_type')->nullable()->after('file_path');
        $table->unsignedBigInteger('file_size')->nullable()->after('file_type');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            //
        });
    }
};
