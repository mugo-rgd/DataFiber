<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nfp_compliances', function (Blueprint $table) {
            // Add columns without referencing other columns
            if (!Schema::hasColumn('nfp_compliances', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }

            if (!Schema::hasColumn('nfp_compliances', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }

            if (!Schema::hasColumn('nfp_compliances', 'fibre_km')) {
                $table->decimal('fibre_km', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('nfp_compliances', 'tower_count')) {
                $table->integer('tower_count')->default(0);
            }
             $table->json('infrastructure')->nullable(); // For storing dynamic infrastructure data
    $table->json('primary_numbers')->nullable();
    $table->json('secondary_numbers')->nullable();
    $table->json('bulk_sms')->nullable();
    $table->json('broadband')->nullable();
    $table->json('staff')->nullable();

        });
    }

    public function down()
    {
        Schema::table('nfp_compliances', function (Blueprint $table) {
            $columns = ['latitude','longitude','fibre_km','tower_count','infrastructure', 'primary_numbers', 'secondary_numbers', 'bulk_sms','broadband','staff'];
            $table->dropColumn($columns);
        });
    }
};
