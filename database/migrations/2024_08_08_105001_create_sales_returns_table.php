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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('sales_return_number', 20);
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
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
