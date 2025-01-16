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
        Schema::create('sale_cancels', function (Blueprint $table) {
            $table->id();

            $table->string('reference_no')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0)->nullable();
            $table->decimal('discount', 10, 2)->default(0)->nullable();
            $table->decimal('vat', 10, 2)->default(0)->nullable();
            $table->decimal('grand_total', 10, 2)->default(0)->nullable();
            $table->decimal('receive_amount', 10, 2)->default(0)->nullable();
            $table->decimal('change_amount', 10, 2)->default(0)->nullable();
            $table->date('date');
            $table->string('status')->nullable();
            $table->string('payment_status')->nullable();
            $table->text('remark')->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->bigInteger('outlet_id')->unsigned()->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->string('waiter_name')->nullable();
            $table->integer('waiter_id')->unsigned()->index();
            $table->string('total_discount_type')->nullable();
            $table->decimal('total_discount_value', 10, 2)->default(0)->nullable();
            $table->decimal('total_discount_amount', 10, 2)->default(0)->nullable();
            $table->decimal('special_discount_value', 10, 2)->default(0)->nullable();
            $table->decimal('special_discount_amount', 10, 2)->default(0)->nullable();
            $table->string('couponCode')->nullable();
            $table->string('couponCodeDiscountType')->nullable();
            $table->decimal('couponCodeDiscountValue', 10, 2)->default(0)->nullable();
            $table->decimal('couponCodeDiscountAmount', 10, 2)->default(0)->nullable();
            $table->decimal('membership_discount_percentage', 10, 2)->default(0)->nullable();
            $table->decimal('membership_discount_amount', 10, 2)->default(0)->nullable();
            $table->bigInteger('exchange_id')->unsigned()->nullable()->nullable();
            $table->bigInteger('company_id')->unsigned()->nullable()->nullable();
            $table->string('delivery_time')->nullable()->nullable();
            $table->decimal('delivery_charge', 10, 2)->default(0)->nullable();
            $table->decimal('additional_charge', 10, 2)->default(0)->nullable();
            $table->decimal('taxable_amount', 10, 2)->default(0)->nullable();

            // Additional columns for cancellation tracking
            $table->bigInteger('cancelled_by')->unsigned()->nullable();
            $table->timestamp('cancelled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_cancels');
    }
};
