<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lease_billing_id')->constrained('lease_billings')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'mpesa', 'paypal', 'cash']);
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['lease_billing_id']);
            $table->index(['transaction_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
