<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignRequestActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('design_request_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->text('description');
            $table->string('icon')->default('circle');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('design_request_activities');
    }
}
