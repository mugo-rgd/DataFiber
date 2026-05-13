<?php
// database/migrations/2024_01_01_000001_create_payment_statements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentStatementsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Changed from customer_id
            $table->string('statement_number')->unique();
            $table->date('statement_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('total_debits', 15, 2)->default(0);
            $table->decimal('total_credits', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->enum('status', ['draft', 'generated', 'sent', 'viewed'])->default('draft');
            $table->string('file_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->index(['user_id', 'statement_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_statements');
    }
}
