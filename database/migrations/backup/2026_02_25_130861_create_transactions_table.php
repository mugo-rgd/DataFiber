<?php
// database/migrations/2024_01_01_000002_create_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Changed from customer_id
            $table->string('transaction_number')->unique();
            $table->date('transaction_date');
            $table->enum('type', ['payment', 'invoice', 'credit', 'debit', 'refund']);
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->enum('direction', ['in', 'out']);
            $table->decimal('balance', 15, 2);
            $table->string('reference')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->index(['user_id', 'transaction_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
