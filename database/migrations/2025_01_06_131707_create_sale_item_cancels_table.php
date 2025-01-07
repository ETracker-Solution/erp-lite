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
        Schema::create('sale_item_cancels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sale_id')->unsigned()->index();
            $table->bigInteger('product_id')->unsigned()->index();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->string('discount_value')->nullable();
            $table->decimal('cogs', 10, 2)->default(0);
            $table->decimal('unit_vat', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->bigInteger('company_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_item_cancels');
    }
};
