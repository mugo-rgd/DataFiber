<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('executive_kpi_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date')->unique();

            $table->decimal('revenue_ksh', 18, 2)->default(0);
            $table->decimal('revenue_usd', 18, 2)->default(0);

            $table->decimal('accounts_receivable_ksh', 18, 2)->default(0);
            $table->decimal('accounts_receivable_usd', 18, 2)->default(0);

            $table->decimal('overdue_ksh', 18, 2)->default(0);
            $table->decimal('overdue_usd', 18, 2)->default(0);

            $table->decimal('quotation_pipeline_ksh', 18, 2)->default(0);
            $table->decimal('quotation_pipeline_usd', 18, 2)->default(0);

            $table->integer('active_leases')->default(0);
            $table->integer('active_contracts')->default(0);

            $table->integer('expiring_contracts_30_days')->default(0);
            $table->integer('expiring_contracts_60_days')->default(0);
            $table->integer('expiring_contracts_90_days')->default(0);

            $table->decimal('total_fibre_km', 12, 2)->default(0);
            $table->decimal('leased_fibre_km', 12, 2)->default(0);
            $table->decimal('available_fibre_km', 12, 2)->default(0);

            $table->integer('total_cores')->default(0);
            $table->integer('used_cores')->default(0);
            $table->integer('available_cores')->default(0);
            $table->decimal('core_utilization_percent', 8, 2)->default(0);

            $table->decimal('sla_compliance_percent', 8, 2)->default(0);
            $table->decimal('network_availability_percent', 8, 2)->default(0);

            $table->timestamps();
        });

        Schema::create('debt_aging_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('currency', 3)->default('KSH');

            $table->decimal('current_amount', 18, 2)->default(0);
            $table->decimal('days_1_30', 18, 2)->default(0);
            $table->decimal('days_31_60', 18, 2)->default(0);
            $table->decimal('days_61_90', 18, 2)->default(0);
            $table->decimal('days_91_120', 18, 2)->default(0);
            $table->decimal('days_120_plus', 18, 2)->default(0);
            $table->decimal('total_outstanding', 18, 2)->default(0);

            $table->integer('billing_count')->default(0);
            $table->integer('overdue_count')->default(0);

            $table->timestamps();

            // Fixed: Shortened index name
            $table->index(['snapshot_date', 'customer_id', 'currency'], 'debt_aging_date_cust_curr_idx');
        });

        Schema::create('revenue_report_snapshots', function (Blueprint $table) {
            $table->id();

            $table->date('period_start');
            $table->date('period_end');

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('lease_id')
                ->nullable()
                ->constrained('leases')
                ->nullOnDelete();

            $table->foreignId('billing_id')
                ->nullable()
                ->constrained('consolidated_billings')
                ->nullOnDelete();

            $table->string('currency', 3)->default('KSH');

            $table->decimal('billed_amount', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('outstanding_amount', 18, 2)->default(0);

            $table->string('service_type')->nullable();
            $table->string('region')->nullable();

            $table->timestamps();

            // Fixed: Shortened index names
            $table->index(['period_start', 'period_end', 'currency'], 'revenue_period_curr_idx');
            $table->index(['customer_id', 'billing_id'], 'revenue_cust_billing_idx');
        });

        Schema::create('quotation_pipeline_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');

            $table->string('currency', 3)->default('KSH');
            $table->string('stage')->nullable();
            $table->string('status')->nullable();

            $table->integer('quotation_count')->default(0);
            $table->decimal('pipeline_value', 18, 2)->default(0);
            $table->decimal('won_value', 18, 2)->default(0);
            $table->decimal('lost_value', 18, 2)->default(0);
            $table->decimal('conversion_rate_percent', 8, 2)->default(0);

            $table->timestamps();

            // Fixed: Shortened index name (original was 71 chars, now 33 chars)
            $table->index(['snapshot_date', 'currency', 'stage', 'status'], 'qp_snapshot_date_curr_stage_stat_idx');
        });

        Schema::create('contract_report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');

            $table->string('currency', 3)->default('KSH');
            $table->string('status')->nullable();

            $table->integer('contract_count')->default(0);
            $table->decimal('contract_value', 18, 2)->default(0);

            $table->integer('expiring_30_days')->default(0);
            $table->integer('expiring_60_days')->default(0);
            $table->integer('expiring_90_days')->default(0);
            $table->decimal('renewal_revenue_at_risk', 18, 2)->default(0);

            $table->timestamps();

            // Fixed: Shortened index name
            $table->index(['snapshot_date', 'currency', 'status'], 'contract_snapshot_date_curr_stat_idx');
        });

        Schema::create('lease_report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');

            $table->string('currency', 3)->default('KSH');
            $table->string('service_type')->nullable();
            $table->string('status')->nullable();
            $table->string('region')->nullable();

            $table->integer('lease_count')->default(0);
            $table->decimal('monthly_revenue', 18, 2)->default(0);
            $table->decimal('contract_value', 18, 2)->default(0);
            $table->decimal('leased_distance_km', 12, 2)->default(0);
            $table->integer('leased_cores')->default(0);

            $table->timestamps();

            // Fixed: Shortened index name
            $table->index(['snapshot_date', 'currency', 'service_type', 'status'], 'lease_snapshot_date_curr_type_stat_idx');
        });

        Schema::create('fiber_utilization_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');

            $table->string('network_id')->nullable();
            $table->string('route_name')->nullable();
            $table->string('region')->nullable();

            $table->decimal('total_fibre_km', 12, 2)->default(0);
            $table->decimal('leased_fibre_km', 12, 2)->default(0);
            $table->decimal('available_fibre_km', 12, 2)->default(0);

            $table->integer('total_cores')->default(0);
            $table->integer('used_cores')->default(0);
            $table->integer('available_cores')->default(0);
            $table->decimal('utilization_percent', 8, 2)->default(0);

            $table->string('capacity_status')->default('normal');

            $table->timestamps();

            // Fixed: Shortened index name
            $table->index(['snapshot_date', 'network_id', 'region'], 'fiber_util_date_net_region_idx');
        });

        Schema::create('sla_network_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');

            $table->foreignId('lease_id')
                ->nullable()
                ->constrained('leases')
                ->nullOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->integer('total_incidents')->default(0);
            $table->integer('open_incidents')->default(0);
            $table->integer('resolved_incidents')->default(0);

            $table->integer('downtime_minutes')->default(0);
            $table->decimal('uptime_percent', 8, 3)->default(100);
            $table->decimal('sla_target_percent', 8, 3)->default(99.95);
            $table->decimal('sla_compliance_percent', 8, 3)->default(100);

            $table->integer('mttr_minutes')->default(0);
            $table->integer('sla_breaches')->default(0);

            $table->timestamps();

            // Fixed: Shortened index names
            $table->index(['snapshot_date', 'lease_id', 'customer_id'], 'sla_date_lease_cust_idx');
            $table->index('lease_id', 'sla_lease_idx');
            $table->index('customer_id', 'sla_customer_idx');
        });

        Schema::create('top_customer_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');

            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('currency', 3)->default('KSH');

            $table->decimal('revenue', 18, 2)->default(0);
            $table->decimal('outstanding_amount', 18, 2)->default(0);
            $table->decimal('revenue_contribution_percent', 8, 2)->default(0);

            $table->integer('active_leases')->default(0);
            $table->integer('active_contracts')->default(0);
            $table->decimal('leased_km', 12, 2)->default(0);
            $table->integer('leased_cores')->default(0);

            $table->string('risk_level')->default('low');

            $table->timestamps();

            // Fixed: Shortened index name
            $table->index(['snapshot_date', 'customer_id', 'currency'], 'top_cust_date_cust_curr_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_customer_snapshots');
        Schema::dropIfExists('sla_network_snapshots');
        Schema::dropIfExists('fiber_utilization_snapshots');
        Schema::dropIfExists('lease_report_snapshots');
        Schema::dropIfExists('contract_report_snapshots');
        Schema::dropIfExists('quotation_pipeline_snapshots');
        Schema::dropIfExists('revenue_report_snapshots');
        Schema::dropIfExists('debt_aging_snapshots');
        Schema::dropIfExists('executive_kpi_snapshots');
    }
};
