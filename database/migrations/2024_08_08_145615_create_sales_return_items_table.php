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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coi_id')->references('id')->on('chart_of_inventories')->onDelete('cascade');
            $table->double('rate', 15, 2);
            $table->double('quantity', 8, 2);
            $table->string('discount_type')->nullable();
            $table->double('discount', 15, 2)->nullable();
            $table->foreignId('sales_return_id')->references('id')->on('sales_returns')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};
