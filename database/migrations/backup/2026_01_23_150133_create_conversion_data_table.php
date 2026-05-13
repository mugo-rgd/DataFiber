<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversion_data', function (Blueprint $table) {
            $table->id();
            $table->string('customer_ref', 50)->nullable();
            $table->string('customer_id', 20)->nullable();
            $table->string('customer_name', 255);
            $table->string('route_name', 255);
            $table->text('links_name');
            $table->integer('cores_leased')->nullable();
            $table->string('bandwidth', 50)->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->decimal('price_per_core_per_km_per_month_usd', 10, 2)->nullable();
            $table->decimal('monthly_link_value_usd', 15, 2)->nullable();
            $table->decimal('monthly_link_kes', 15, 2)->nullable();
            $table->string('link_class', 50)->nullable();
            $table->integer('contract_duration_yrs')->nullable();
            $table->decimal('total_contract_value_usd', 15, 2)->nullable();
            $table->decimal('total_contract_value_kes', 15, 2)->nullable();
            $table->timestamps();

            // Use custom index names to avoid MySQL 64-character limit
            $table->index(['customer_name', 'link_class'], 'idx_customer_link');
            $table->index(['contract_duration_yrs', 'total_contract_value_usd'], 'idx_duration_value');

            // Single column indexes
            $table->index('customer_ref');
            $table->index('customer_name');
            $table->index('link_class');
            $table->index('contract_duration_yrs');
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversion_data');
    }
};
