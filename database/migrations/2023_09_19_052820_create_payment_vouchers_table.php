<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->date('date');
            $table->double('amount',16,2);
            $table->foreignId('credit_account_id')->comment('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->foreignId('debit_account_id')->comment('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->string('payee_name')->nullable();
            $table->longText('narration')->nullable();
            $table->string('reference_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_vouchers');
    }
}
