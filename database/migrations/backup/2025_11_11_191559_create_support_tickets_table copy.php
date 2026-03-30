<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lease_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->enum('category', ['technical', 'billing', 'service', 'other']);
            $table->text('description');
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('lease_id');
            $table->index('status');
            $table->index('ticket_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
};
