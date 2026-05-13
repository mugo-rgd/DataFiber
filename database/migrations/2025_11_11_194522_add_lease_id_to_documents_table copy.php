<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, add the column as nullable
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'lease_id')) {
                $table->foreignId('lease_id')->nullable()->after('id');
            }
        });

        // If there are existing documents, assign them to a valid lease or set to null
        // This assumes you have at least one lease in the database
        if (Schema::hasTable('leases') && DB::table('leases')->exists()) {
            $firstLease = DB::table('leases')->first();
            DB::table('documents')->update(['lease_id' => $firstLease->id]);
        }

        // Now make the column required and add foreign key constraint
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('lease_id')->nullable(false)->change();
            $table->foreign('lease_id')->references('id')->on('leases')->onDelete('cascade');
            $table->index('lease_id');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['lease_id']);
            $table->dropColumn('lease_id');
        });
    }
};
