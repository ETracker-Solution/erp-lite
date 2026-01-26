<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->string('waiter_id')->nullable();
            $table->string('waiter_name')->nullable();
            $table->string('membership_discount_percentage')->nullable();
            $table->string('membership_discount_amount')->nullable();
            $table->string('special_discount_value')->nullable();
            $table->string('special_discount_amount')->nullable();
            $table->string('couponCode')->nullable();
            $table->string('couponCodeDiscountType')->nullable();
            $table->string('couponCodeDiscountValue')->nullable();
            $table->string('couponCodeDiscountAmount')->nullable();
            $table->string('total_discount_type')->nullable();
            $table->string('total_discount_value')->nullable();
            $table->string('total_discount_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->dropColumn(['waiter_id', 'waiter_name']);
            $table->dropColumn('membership_discount_percentage');
            $table->dropColumn('membership_discount_amount');
            $table->dropColumn('special_discount_value');
            $table->dropColumn('special_discount_amount');
            $table->dropColumn('couponCode');
            $table->dropColumn('couponCodeDiscountType');
            $table->dropColumn('couponCodeDiscountValue');
            $table->dropColumn('couponCodeDiscountAmount');
            $table->dropColumn('total_discount_type');
            $table->dropColumn('total_discount_value');
            $table->dropColumn('total_discount_amount');
        });
    }
};
