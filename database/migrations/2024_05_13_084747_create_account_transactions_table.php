<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('transaction_type')->comment('1 = Debit, -1 = Credit');
            $table->enum('type', ['debit', 'credit']);
            $table->double('amount', 16, 2);
            $table->bigInteger('transaction_id');
            $table->string('payee_name')->nullable();
            $table->longText('narration')->nullable();
            $table->string('reference_no')->nullable();
            $table->foreignId('chart_of_account_id')->comment('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->bigInteger('doc_id');
            $table->string('doc_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_transactions');
    }
};
