<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type'); // payment, refund, credit, adjustment
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2)->default(0);
            $table->decimal('balance_after', 10, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->string('status')->default('completed'); // pending, completed, failed, cancelled
            $table->string('reference')->nullable()->unique(); // Internal reference number
            $table->string('gateway_transaction_id')->nullable(); // External gateway ID
            $table->string('gateway_name')->nullable(); // stripe, paypal, etc.
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['processed_at', 'status']);
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
