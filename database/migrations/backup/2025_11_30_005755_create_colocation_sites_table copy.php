<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_colocation_sites_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('colocation_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_request_id')->constrained()->onDelete('cascade');
            $table->string('site_name');
            $table->enum('service_type', ['shelter_space', 'rack', 'cage', 'suites']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('colocation_sites');
    }
};
