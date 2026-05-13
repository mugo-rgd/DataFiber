<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('document_requests', 'additional_notes')) {
                $table->text('additional_notes')->nullable()->after('document_types');
            }
        });
    }

    public function down()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            if (Schema::hasColumn('document_requests', 'additional_notes')) {
                $table->dropColumn('additional_notes');
            }
        });
    }
};
