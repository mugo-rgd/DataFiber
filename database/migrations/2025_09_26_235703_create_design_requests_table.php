<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('designer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('request_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('pending'); // pending, in_design, designed, quoted, approved, rejected
            $table->text('technical_requirements')->nullable();
            $table->text('design_specifications')->nullable();
            $table->text('design_notes')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('quoted_amount', 10, 2)->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('design_completed_at')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['designer_id', 'status']);
            $table->index('request_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_requests');
    }
};
