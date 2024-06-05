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
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20);
            $table->date('order_date')->nullable();
            $table->double('subtotal', 15, 2);
            $table->double('discount', 8, 2)->default(0);
            $table->double('vat', 8, 2)->nullable();
            $table->double('grand_total', 15, 2);
            $table->double('advance_amount', 15, 2)->default(0);
            $table->text('remark')->nullable();
            $table->string('image')->nullable();
            $table->string('order_from')->nullable();
            $table->string('paid_by')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('customer_number');
            $table->string('sale_id')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_orders');
    }
};
