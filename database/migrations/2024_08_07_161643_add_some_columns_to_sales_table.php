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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('total_discount_type')->nullable();
            $table->string('total_discount_value')->nullable();
            $table->string('total_discount_amount')->nullable();
            $table->string('special_discount_value')->nullable();
            $table->string('special_discount_amount')->nullable();
            $table->string('couponCode')->nullable();
            $table->string('couponCodeDiscountType')->nullable();
            $table->string('couponCodeDiscountValue')->nullable();
            $table->string('couponCodeDiscountAmount')->nullable();
            $table->string('membership_discount_percentage')->nullable();
            $table->string('membership_discount_value')->nullable();
            $table->string('exchange_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['total_discount_type', 'total_discount_value', 'total_discount_amount', 'special_discount_value', 'special_discount_amount', 'couponCode', 'couponCodeDiscountType', 'couponCodeDiscountValue', 'couponCodeDiscountAmount', 'membership_discount_percentage', 'membership_discount_value']);
        });
    }
};
