<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('network_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // router, switch, firewall, etc.
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('location');
            $table->string('status')->default('active'); // active, maintenance, offline
            $table->string('ip_address')->nullable();
            $table->json('specifications')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('network_equipment');
    }
};
