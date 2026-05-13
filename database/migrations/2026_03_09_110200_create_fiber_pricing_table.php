<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fiber_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('link_type')->unique();
            $table->decimal('base_rate_km_kes', 10, 2);
            $table->decimal('base_rate_km_usd', 10, 2);
            $table->integer('volume_discount_threshold')->default(0);
            $table->decimal('volume_discount_percent', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fiber_pricing');
    }
};
