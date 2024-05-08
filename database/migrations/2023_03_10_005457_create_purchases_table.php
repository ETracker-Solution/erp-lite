<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('serial_no')->unique();
            $table->enum('status', ['pending', 'received','ordered'])->default('received');
            $table->date('date')->nullable();
            $table->double('subtotal', 15, 2);
            $table->double('vat', 15, 2)->default(0);
            $table->double('grand_total', 15, 2);
            $table->double('net_payable', 15, 2);
            $table->text('remark')->nullable();
            $table->foreignId('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('purchases');
    }
}
