<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 20)->nullable();
            $table->string('invoice_number', 20);
            $table->double('subtotal', 15, 2);
            $table->double('discount', 8, 2)->default(0);
            $table->double('vat', 8, 2)->nullable();
            $table->double('grand_total', 15, 2);
            $table->double('receive_amount', 15, 2)->default(0);
            $table->double('change_amount', 15, 2)->default(0);
            $table->date('date')->nullable();
            $table->string('status')->nullable()->default('final');
            $table->string('payment_status')->nullable()->default('due');
            $table->text('remark')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('cascade');
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
        Schema::dropIfExists('sales');
    }
}
