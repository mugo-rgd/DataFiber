<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKPIHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('kpi_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_manager_id');
            $table->date('snapshot_date');

            // USD Metrics
            $table->decimal('total_mrr_usd', 12, 2)->default(0);
            $table->decimal('total_tcv_usd', 15, 2)->default(0);
            $table->decimal('arpc_usd', 12, 2)->default(0);

            // KSH Metrics
            $table->decimal('total_mrr_ksh', 15, 2)->default(0);
            $table->decimal('total_tcv_ksh', 18, 2)->default(0);
            $table->decimal('arpc_ksh', 15, 2)->default(0);

            // Combined Metrics
            $table->decimal('total_mrr_combined', 15, 2)->default(0);
            $table->decimal('total_tcv_combined', 18, 2)->default(0);

            // Portfolio Metrics
            $table->integer('total_customers')->default(0);
            $table->integer('total_leases')->default(0);
            $table->integer('active_leases')->default(0);
            $table->integer('terminated_leases')->default(0);

            // Utilization Metrics
            $table->decimal('total_distance_km', 12, 2)->default(0);
            $table->integer('total_cores_leased')->default(0);

            // Contract Health
            $table->decimal('avg_contract_term_years', 5, 2)->default(0);
            $table->integer('short_term_contracts')->default(0);
            $table->integer('mid_term_contracts')->default(0);
            $table->integer('long_term_contracts')->default(0);
            $table->integer('upcoming_renewals_90days')->default(0);
            $table->decimal('renewal_revenue_at_risk', 12, 2)->default(0);

            // Customer Health
            $table->decimal('churn_rate', 5, 2)->default(0);

            // Performance
            $table->integer('performance_score')->default(0);
            $table->string('performance_rating', 50);

            // JSON data for detailed breakdown
            $table->json('technology_mix')->nullable();
            $table->json('customer_breakdown')->nullable();
            $table->json('snapshot_data')->nullable();

            $table->timestamps();

            $table->foreign('account_manager_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['account_manager_id', 'snapshot_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpi_history');
    }
}
