<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('billing_line_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consolidated_billing_id');
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_cycle'); // monthly, quarterly, annually
            $table->date('period_start');
            $table->date('period_end');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['consolidated_billing_id', 'lease_id']);
        });

        // Add foreign key constraint separately after both tables exist
        Schema::table('billing_line_items', function (Blueprint $table) {
            $table->foreign('consolidated_billing_id')
                  ->references('id')
                  ->on('consolidated_billings')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('billing_line_items', function (Blueprint $table) {
            $table->dropForeign(['consolidated_billing_id']);
        });

        Schema::dropIfExists('billing_line_items');
    }
};
