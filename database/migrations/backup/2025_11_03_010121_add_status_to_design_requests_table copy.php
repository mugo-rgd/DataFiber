<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_status_to_design_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('design_requests', function (Blueprint $table) {
            // $table->string('status')->default('draft')->after('description');
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->foreignId('quotation_id')->nullable()->after('approved_at');
        });
    }

    public function down()
    {
        Schema::table('design_requests', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'quotation_id']);
        });
    }
};
