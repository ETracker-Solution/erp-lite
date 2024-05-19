<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->date('date');
            $table->double('amount',16,2);
            $table->foreignId('debit_account_id')->comment('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->foreignId('credit_account_id')->comment('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->longText('narration')->nullable();
            $table->string('reference_no')->nullable();
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
        Schema::dropIfExists('journal_vouchers');
    }
}
