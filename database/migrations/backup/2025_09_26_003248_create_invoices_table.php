<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('status')->default('draft'); // draft, sent, viewed, paid, overdue, cancelled
            $table->text('description')->nullable();
            $table->json('line_items')->nullable(); // Invoice line items as JSON
            $table->text('notes')->nullable();
            $table->string('payment_terms')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
