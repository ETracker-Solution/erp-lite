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
        Schema::create('delivery_cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->string('other_outlet_sale_id');
            $table->date('date');
            $table->double('amount',16,2);
            $table->foreignId('credit_account_id')->comment('chart_of_account_id,from')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->foreignId('debit_account_id')->comment('chart_of_account_id,to')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->longText('narration')->nullable();
            $table->string('reference_no')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_cash_transfers');
    }
};
