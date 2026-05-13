<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // Create migration: php artisan make:migration create_debt_management_tables

public function up()
{
    // Payment Plans Table
    Schema::create('payment_plans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('consolidated_billing_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->decimal('total_amount', 15, 2);
        $table->decimal('down_payment', 15, 2)->default(0);
        $table->integer('installment_count');
        $table->decimal('installment_amount', 15, 2);
        $table->date('start_date');
        $table->date('end_date');
        $table->enum('status', ['active', 'completed', 'defaulted', 'cancelled'])->default('active');
        $table->json('terms')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });

    // Payment Plan Installments
    Schema::create('payment_plan_installments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('payment_plan_id')->constrained()->onDelete('cascade');
        $table->integer('installment_number');
        $table->decimal('amount', 15, 2);
        $table->date('due_date');
        $table->date('paid_date')->nullable();
        $table->decimal('paid_amount', 15, 2)->default(0);
        $table->enum('status', ['pending', 'paid', 'overdue', 'partial'])->default('pending');
        $table->text('notes')->nullable();
        $table->timestamps();
    });

    // Debt Collection Actions
    Schema::create('debt_collection_actions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('consolidated_billing_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('action_type', ['reminder', 'call', 'email', 'sms', 'visit', 'legal']);
        $table->text('action_details');
        $table->text('response')->nullable();
        $table->date('follow_up_date')->nullable();
        $table->foreignId('assigned_to')->nullable()->constrained('users');
        $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
        $table->timestamps();
    });

    // Debt Write-offs
    Schema::create('debt_write_offs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('consolidated_billing_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->decimal('amount', 15, 2);
        $table->enum('write_off_type', ['bad_debt', 'dispute', 'courtesy', 'other']);
        $table->text('reason');
        $table->text('approval_notes')->nullable();
        $table->foreignId('approved_by')->nullable()->constrained('users');
        $table->timestamp('approved_at')->nullable();
        $table->timestamps();
    });

    // Collection Agency Assignments
    Schema::create('collection_agency_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('consolidated_billing_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('agency_name');
        $table->decimal('assigned_amount', 15, 2);
        $table->decimal('commission_rate', 5, 2)->default(0);
        $table->date('assignment_date');
        $table->date('recovery_date')->nullable();
        $table->decimal('recovered_amount', 15, 2)->default(0);
        $table->enum('status', ['active', 'recovered', 'closed', 'cancelled'])->default('active');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_management_tables');
    }
};
