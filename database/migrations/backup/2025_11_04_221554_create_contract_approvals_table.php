<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_contract_approvals_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractApprovalsTable extends Migration
{
    public function up()
    {
        Schema::create('contract_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->enum('approved_by', ['customer', 'admin']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_approvals');
    }
}
