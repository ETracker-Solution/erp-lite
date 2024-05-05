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
            $table->string('batch_number');
            $table->string('reference_no');
            $table->string('purchase_number');
            $table->enum('status', ['pending', 'received','ordered'])->default('received');
            $table->date('date');
            $table->double('subtotal', 15, 2);
            $table->double('discount', 15, 2)->default(0);
            $table->double('carrying_cost', 15, 2)->default(0);
            $table->double('grand_total', 15, 2);
            $table->text('description')->nullable();
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
