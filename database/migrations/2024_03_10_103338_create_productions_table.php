<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('production_no')->nullable();
            $table->string('batch_no')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('date')->nullable();
            $table->double('subtotal', 15, 2)->nullable();
            $table->double('vat_total', 15, 2)->nullable();
            $table->double('discount', 15, 2)->nullable();
            $table->double('grand_total', 15, 2)->nullable();
            $table->text('remark')->nullable();
            $table->string('status')->default('pending');
            $table->string('type')->default('in');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('productions');
    }
}
