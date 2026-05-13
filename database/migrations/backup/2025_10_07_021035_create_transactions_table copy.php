<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_create_transactions_table.php
public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();

        // Transaction Details
        $table->string('transaction_number')->unique();
        $table->enum('type', ['income', 'expense']);
        $table->string('category'); // invoice_payment, salary, equipment, maintenance, utilities, office_supplies, travel, other
        $table->decimal('amount', 12, 2);
        $table->string('currency', 3)->default('USD');

        // Dates
        $table->date('transaction_date');
        $table->date('due_date')->nullable();

        // Payment Information
        $table->string('payment_method'); // bank_transfer, credit_card, cash, check, online
        $table->string('payment_reference')->nullable();
        $table->string('status')->default('pending'); // pending, completed, cancelled, failed

        // Description and References
        $table->text('description');
        $table->text('notes')->nullable();
        $table->string('reference_number')->nullable();

        // Relationships
        $table->foreignId('billing_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('created_by')->constrained('users');
        $table->foreignId('approved_by')->nullable()->constrained('users');

        // Timestamps
        $table->timestamp('processed_at')->nullable();
        $table->softDeletes();
        $table->timestamps();

        // Indexes
        $table->index(['transaction_date', 'type']);
        $table->index(['type', 'status']);
        $table->index('billing_id');
    });
}
};
