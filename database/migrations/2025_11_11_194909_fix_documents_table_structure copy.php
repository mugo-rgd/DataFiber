<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, check if documents table exists
        if (!Schema::hasTable('documents')) {
            // Create the documents table if it doesn't exist
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lease_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('name');
                $table->enum('type', ['contract', 'certificate', 'report', 'other'])->default('other');
                $table->string('file_path');
                $table->integer('file_size')->default(0);
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('lease_id');
                $table->index('status');
                $table->index('type');
            });
        } else {
            // If table exists, add lease_id as nullable first
            if (!Schema::hasColumn('documents', 'lease_id')) {
                Schema::table('documents', function (Blueprint $table) {
                    $table->foreignId('lease_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                    $table->index('lease_id');
                });
            }
        }
    }

    public function down()
    {
        // Don't drop the table in down method to preserve data
        if (Schema::hasColumn('documents', 'lease_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropForeign(['lease_id']);
                $table->dropColumn('lease_id');
            });
        }
    }
};
