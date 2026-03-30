<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Add missing columns
            $table->json('line_items')->nullable()->after('quotation_number');
            $table->decimal('subtotal', 10, 2)->default(0)->after('line_items');
            $table->decimal('tax_rate', 5, 4)->default(0.16)->after('subtotal');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            $table->decimal('total_amount', 10, 2)->default(0)->after('tax_amount');
            $table->string('status')->default('draft')->after('total_amount');
            $table->dateTime('valid_until')->nullable()->after('status');
            $table->timestamp('sent_at')->nullable()->after('valid_until');
            $table->text('scope_of_work')->nullable()->after('sent_at');
            $table->text('terms_and_conditions')->nullable()->after('scope_of_work');

            // Add indexes
            $table->index('quotation_number');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'line_items',
                'subtotal',
                'tax_rate',
                'tax_amount',
                'total_amount',
                'status',
                'valid_until',
                'sent_at',
                'scope_of_work',
                'terms_and_conditions'
            ]);

            $table->dropIndex(['quotation_number']);
            $table->dropIndex(['status']);
        });
    }
};
