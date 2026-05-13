<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('credit_card'); // credit_card, bank_transfer, etc.
            $table->string('transaction_id')->nullable()->unique(); // External payment gateway ID
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->text('description')->nullable();
            $table->date('payment_date');
            $table->date('due_date')->nullable();
            $table->json('metadata')->nullable(); // Additional payment data
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['payment_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
