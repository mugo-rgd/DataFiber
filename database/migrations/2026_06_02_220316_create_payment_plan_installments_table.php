<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_plan_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_plan_id');
            $table->integer('installment_number');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'partial', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign key with custom short name
            $table->foreign('payment_plan_id', 'fk_ppi_plan')
                  ->references('id')
                  ->on('payment_plans')
                  ->onDelete('cascade');

            // Indexes with custom short names
            $table->index(['payment_plan_id', 'installment_number'], 'idx_ppi_plan_inst');
            $table->index('due_date', 'idx_ppi_duedate');
            $table->index('status', 'idx_ppi_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plan_installments');
    }
};
