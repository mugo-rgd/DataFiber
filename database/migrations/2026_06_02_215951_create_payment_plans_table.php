<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consolidated_billing_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('down_payment', 15, 2)->default(0);
            $table->integer('installment_count');
            $table->decimal('installment_amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('frequency', ['weekly', 'biweekly', 'monthly', 'quarterly'])->default('monthly');
            $table->enum('status', ['active', 'completed', 'cancelled', 'defaulted'])->default('active');
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign keys with custom short names
            $table->foreign('consolidated_billing_id', 'fk_pp_billing')
                  ->references('id')
                  ->on('consolidated_billings')
                  ->onDelete('cascade');

            $table->foreign('user_id', 'fk_pp_user')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Indexes with custom short names
            $table->index(['status', 'end_date'], 'idx_pp_status_date');
            $table->index('user_id', 'idx_pp_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};
