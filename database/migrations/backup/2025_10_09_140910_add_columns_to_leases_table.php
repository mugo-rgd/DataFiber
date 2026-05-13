<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leases', function (Blueprint $table) {
            // Foreign keys
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->onDelete('set null');
            $table->foreignId('design_request_id')->nullable()->constrained('design_requests')->onDelete('set null');

            // Lease identification
            $table->string('lease_number')->unique()->after('id');
            $table->string('title')->nullable()->after('lease_number');

            // Service details
            $table->enum('service_type', ['dark_fibre', 'wavelength', 'ethernet', 'ip_transit', 'colocation'])->default('dark_fibre')->after('title');
            $table->string('bandwidth')->nullable()->after('service_type'); // e.g., "2 cores", "10Gbps", "1RU"
            $table->enum('technology', ['single_mode', 'multimode', 'dwdm', 'cwdm', 'other'])->nullable()->after('bandwidth');

            // Route information
            $table->string('start_location')->after('technology');
            $table->string('end_location')->after('start_location');
            $table->decimal('distance_km', 8, 2)->nullable()->after('end_location');

            // Pricing information
            $table->decimal('monthly_cost', 12, 2)->default(0)->after('distance_km');
            $table->decimal('installation_fee', 12, 2)->default(0)->after('monthly_cost');
            $table->decimal('total_contract_value', 12, 2)->default(0)->after('installation_fee');
            $table->string('currency', 3)->default('USD')->after('total_contract_value');

            // Contract period
            $table->date('start_date')->after('currency');
            $table->date('end_date')->after('start_date');
            $table->integer('contract_term_months')->default(12)->after('end_date');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'annually', 'one_time'])->default('monthly')->after('contract_term_months');

            // Status and dates
            $table->enum('status', ['draft', 'pending', 'active', 'expired', 'terminated', 'cancelled'])->default('draft')->after('billing_cycle');
            $table->timestamp('sent_at')->nullable()->after('status');
            $table->timestamp('accepted_at')->nullable()->after('sent_at');
            $table->timestamp('activated_at')->nullable()->after('accepted_at');
            $table->timestamp('terminated_at')->nullable()->after('activated_at');

            // Technical specifications
            $table->text('technical_specifications')->nullable()->after('terminated_at');
            $table->text('service_level_agreement')->nullable()->after('technical_specifications');
            $table->text('terms_and_conditions')->nullable()->after('service_level_agreement');
            $table->text('special_requirements')->nullable()->after('terms_and_conditions');

            // Additional information
            $table->text('notes')->nullable()->after('special_requirements');
            $table->json('attachments')->nullable()->after('notes'); // Store file paths or metadata

            // Indexes for better performance
            $table->index('lease_number');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['status', 'end_date']);
        });
    }

    public function down()
    {
        Schema::table('leases', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['quotation_id']);
            $table->dropForeign(['design_request_id']);

            // Drop all added columns
            $table->dropColumn([
                'customer_id',
                'quotation_id',
                'design_request_id',
                'lease_number',
                'title',
                'service_type',
                'bandwidth',
                'technology',
                'start_location',
                'end_location',
                'distance_km',
                'monthly_cost',
                'installation_fee',
                'total_contract_value',
                'currency',
                'start_date',
                'end_date',
                'contract_term_months',
                'billing_cycle',
                'status',
                'sent_at',
                'accepted_at',
                'activated_at',
                'terminated_at',
                'technical_specifications',
                'service_level_agreement',
                'terms_and_conditions',
                'special_requirements',
                'notes',
                'attachments'
            ]);

            // Drop indexes
            $table->dropIndex(['lease_number']);
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['status', 'end_date']);
        });
    }
};
