<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->double('amount', 15, 2);
            $table->integer('transaction_type')->comment('-1 = payment,1 = purchase');
            $table->text('description')->nullable();
            $table->string('doc_type')->nullable();
            $table->integer('doc_id')->nullable();
            $table->foreignId('chart_of_account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreignId('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
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
        Schema::dropIfExists('supplier_transactions');
    }
}
