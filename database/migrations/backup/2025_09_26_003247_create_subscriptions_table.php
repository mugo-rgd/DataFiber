<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Basic Fibre Plan", "Enterprise Plan"
            $table->string('stripe_id')->nullable()->unique(); // Stripe subscription ID
            $table->string('stripe_status')->nullable();
            $table->string('stripe_price')->nullable(); // Stripe price ID
            $table->integer('quantity')->default(1);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('interval')->default('month'); // day, week, month, year
            $table->integer('interval_count')->default(1);
            $table->string('status')->default('active'); // active, cancelled, past_due, unpaid
            $table->date('start_date');
            $table->date('next_billing_date');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('next_billing_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
