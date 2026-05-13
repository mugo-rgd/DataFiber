<?php
// database/migrations/2024_01_01_000001_create_company_profiles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('kra_pin', 20);
            $table->string('phone_number', 20);
            $table->string('registration_number', 100)->comment('Company registration cert number');
            $table->enum('company_type', ['public', 'parastatal', 'county government', 'private', 'NGO']);
            $table->string('contact_name_1', 255);
            $table->string('contact_phone_1', 20);
            $table->string('contact_name_2', 255)->nullable();
            $table->string('contact_phone_2', 20)->nullable();
            $table->string('physical_location', 255);
            $table->string('road', 255);
            $table->string('town', 255);
            $table->string('address', 255);
            $table->string('code', 50);
            $table->text('description')->nullable();
            $table->string('profile_photo')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Unique constraints
            $table->unique('kra_pin');
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_profiles');
    }
}
