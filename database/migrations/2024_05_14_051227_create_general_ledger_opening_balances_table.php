<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('general_ledger_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->date('date');
            $table->double('amount', 8, 2);
            $table->foreignId('coia_id')->comment('Chart Of Account Id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');
            $table->enum('account_type',['as','li','in','ex'])->comment('plz replace with root account type')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_ledger_opening_balances');
    }
};
