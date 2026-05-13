<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('automated_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_number');
            $table->decimal('amount', 10, 2);
            $table->date('billing_date');
            $table->date('due_date');
            $table->enum('status', ['generated', 'sent', 'paid', 'failed'])->default('generated');
            $table->text('description');
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'billing_date']);
            $table->unique(['invoice_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('automated_billings');
    }
};
