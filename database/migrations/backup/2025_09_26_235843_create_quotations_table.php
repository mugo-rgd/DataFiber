<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_request_id')->constrained()->onDelete('cascade');
            $table->string('quotation_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->text('scope_of_work');
            $table->text('terms_and_conditions');
            $table->date('valid_until');
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index('design_request_id');
            $table->index('quotation_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
