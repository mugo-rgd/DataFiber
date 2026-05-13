<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();

            // Invoice Information
            $table->string('invoice_number')->unique();
            $table->string('invoice_type')->default('invoice'); // invoice, credit_note, debit_note, proforma
            $table->string('reference_number')->nullable(); // External reference
            $table->string('billing_cycle')->default('monthly'); // monthly, quarterly, annually, one_time

            // Customer Information
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('lease_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('design_request_id')->nullable()->constrained()->onDelete('set null');

            // Billing Details
            $table->string('billing_type'); // lease_rental, maintenance, installation, consultation, equipment, emergency_service
            $table->decimal('subtotal_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('tax_rate', 5, 2)->default(0); // Tax rate in percentage

            // Dates
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->date('period_start_date')->nullable(); // For recurring bills
            $table->date('period_end_date')->nullable();   // For recurring bills

            // Status and Payment
            $table->string('status')->default('draft'); // draft, sent, viewed, partial, paid, overdue, cancelled, refunded
            $table->string('payment_method')->nullable(); // bank_transfer, credit_card, cash, check, online
            $table->string('payment_reference')->nullable();
            $table->text('payment_notes')->nullable();

            // Service Details
            $table->text('service_description');
            $table->text('terms_conditions')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();

            // Technical Details (for fiber optic services)
            $table->string('service_location')->nullable();
            $table->string('circuit_id')->nullable();
            $table->string('port_number')->nullable();
            $table->decimal('bandwidth_mbps', 10, 2)->nullable();
            $table->string('service_level')->nullable(); // SLA level

            // Administrative
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes for performance
            $table->index(['invoice_date', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('lease_id');
        });

        // Create billing_items table for line items
        Schema::create('billing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_id')->constrained()->onDelete('cascade');
            $table->string('item_type'); // service, equipment, labor, material, fee
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit')->nullable(); // hours, units, mbps, etc.
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('billing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_items');
        Schema::dropIfExists('billings');
    }
};
