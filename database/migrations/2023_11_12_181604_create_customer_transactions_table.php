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
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->double('amount', 15, 2);
            $table->integer('transaction_type')->comment('-1 = receive,1 = sale');
            $table->text('description')->nullable();
            $table->string('doc_type')->nullable();
            $table->integer('doc_id')->nullable();
            $table->foreignId('chart_of_account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transactions');
    }
};
