<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contract_approvals', function (Blueprint $table) {
            // Change approved_by from ENUM to foreign key
            $table->dropColumn('approved_by');
        });

        Schema::table('contract_approvals', function (Blueprint $table) {
            $table->foreignId('approved_by')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('contract_approvals', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
        });

        Schema::table('contract_approvals', function (Blueprint $table) {
            $table->enum('approved_by', ['admin', 'manager'])->nullable();
        });
    }
};
