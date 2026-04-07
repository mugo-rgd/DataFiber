<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, json, boolean, integer
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default exchange rate
        DB::table('settings')->insert([
            [
                'key' => 'usd_to_kes_rate',
                'value' => '130.00',
                'type' => 'decimal',
                'description' => 'USD to KES exchange rate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'exchange_rate_source',
                'value' => 'manual',
                'type' => 'string',
                'description' => 'Exchange rate source (manual/api)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'auto_billing_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable automatic billing generation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
