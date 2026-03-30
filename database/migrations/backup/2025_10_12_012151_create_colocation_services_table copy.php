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
        Schema::create('colocation_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_number')->unique(); // COLO-20241011-001
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Changed from customer_id to user_id
            $table->foreignId('design_request_id')->nullable()->constrained()->onDelete('cascade');

            // Service Details
            $table->string('service_type'); // rack_space, cabinet, cage, private_suite
            $table->integer('rack_units')->default(1); // U size for rack space
            $table->string('cabinet_size')->nullable(); // full_cabinet, half_cabinet, quarter_cabinet
            $table->string('location_reference'); // e.g., A12, B05, Suite-101

            // Power Requirements
            $table->decimal('power_amps', 8, 2)->default(10);
            $table->string('power_type')->default('single_phase'); // single_phase, three_phase
            $table->integer('power_circuits')->default(1);

            // Network
            $table->integer('network_ports')->default(1);
            $table->string('port_speed')->default('1G'); // 100M, 1G, 10G

            // Pricing
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->string('billing_cycle')->default('monthly');

            // Contract
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('contract_months')->default(12);

            // Status
            $table->string('status')->default('active'); // active, suspended, terminated

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'status']); // Updated to user_id
            $table->index(['design_request_id']);
            $table->index('service_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colocation_services');
    }
};
