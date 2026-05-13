<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('consolidated_billings', function (Blueprint $table) {
            $table->id();
            $table->string('billing_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // References users table
            $table->date('billing_date');
            $table->date('due_date');
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'pending', 'sent', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'billing_date']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('consolidated_billings');
    }
};
