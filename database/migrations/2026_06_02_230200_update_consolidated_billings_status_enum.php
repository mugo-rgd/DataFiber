<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For MySQL
        DB::statement("ALTER TABLE consolidated_billings
            MODIFY COLUMN status ENUM(
                'pending',
                'sent',
                'paid',
                'partial',
                'overdue',
                'cancelled',
                'draft',
                'payment_plan'
            ) DEFAULT 'pending' NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE consolidated_billings
            MODIFY COLUMN status ENUM(
                'pending',
                'sent',
                'paid',
                'partial',
                'overdue',
                'cancelled',
                'draft'
            ) DEFAULT 'pending' NOT NULL");
    }
};
