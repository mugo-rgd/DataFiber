<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_document_types_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTypesTable extends Migration
{
    public function up()
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('document_type')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('max_file_size')->default(2048); // in KB
            $table->json('allowed_extensions')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_types');
    }
}
