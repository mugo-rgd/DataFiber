<?php
// database/migrations/2025_10_19_212845_create_financial_parameters_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialParametersTable extends Migration
{
    public function up()
    {
        Schema::create('financial_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('parameter_name', 50); // e.g., 'vat_rate', 'kes_to_usd'
            $table->decimal('parameter_value', 10, 6); // 20.000000 for 20%, 0.009200 for exchange rate
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('currency_code', 3)->nullable(); // KES, USD, etc.
            $table->string('country_code', 3)->default('KEN'); // For country-specific parameters
            $table->text('description')->nullable();

            // Audit fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes for performance - using shorter names
            $table->index(['parameter_name', 'effective_from', 'effective_to'], 'fin_params_name_eff_idx');
            $table->index(['parameter_name', 'currency_code'], 'fin_params_name_curr_idx');
            $table->index('country_code', 'fin_params_country_idx');
            $table->index('effective_from', 'fin_params_eff_from_idx');
            $table->index('effective_to', 'fin_params_eff_to_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_parameters');
    }
}
