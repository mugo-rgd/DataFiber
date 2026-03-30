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
        DB::statement("ALTER TABLE contracts MODIFY COLUMN status ENUM('draft', 'pending_approval', 'sent_to_customer', 'approved', 'rejected') DEFAULT 'draft'");

        // Alternative for other databases:
        // Schema::table('contracts', function (Blueprint $table) {
        //     $table->enum('status', [
        //         'draft',
        //         'pending_approval',
        //         'sent_to_customer',
        //         'approved',
        //         'rejected'
        //     ])->default('draft')->change();
        // });
    }

    public function down()
    {
        DB::statement("ALTER TABLE contracts MODIFY COLUMN status ENUM('draft', 'pending_approval', 'approved', 'rejected') DEFAULT 'draft'");
    }
};
