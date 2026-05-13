<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('billings', function (Blueprint $table) {
            // Drop the existing foreign key constraint (if it exists)
            $table->dropForeign(['customer_id']);

            // Add new foreign key constraint pointing to users table
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);

            // Note: We can't restore the original since customers table doesn't exist
            // You might need to create the customers table first if rolling back
        });
    }
};
