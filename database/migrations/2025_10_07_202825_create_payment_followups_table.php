<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('account_manager_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'reminded', 'paid', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Index for better performance
            $table->index(['account_manager_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_followups');
    }
};
