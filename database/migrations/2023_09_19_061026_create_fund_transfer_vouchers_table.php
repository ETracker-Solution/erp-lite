<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundTransferVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_transfer_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->date('date');
            $table->double('amount',16,2);
            $table->foreignId('credit_account_id')->comment('chart_of_account_id,from')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->foreignId('debit_account_id')->comment('chart_of_account_id,to')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->longText('narration')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('fund_transfer_vouchers');
    }
}
