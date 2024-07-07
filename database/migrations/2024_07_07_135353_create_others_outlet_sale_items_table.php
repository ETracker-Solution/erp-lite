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
        Schema::create('others_outlet_sale_items', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->double('unit_price', 16, 2);
            $table->double('quantity', 16, 2);
            $table->string('discount_type')->nullable();
            $table->double('discount', 16, 2)->nullable();
            $table->foreignId('others_outlet_sale_id')->references('id')->on('others_outlet_sales')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('others_outlet_sale_items');
    }
};
