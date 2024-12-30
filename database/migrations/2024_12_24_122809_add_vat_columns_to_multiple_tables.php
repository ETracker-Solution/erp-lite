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
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('unit_vat', 10, 2)->nullable();
            $table->decimal('vat_amount', 10, 2)->nullable();
            $table->decimal('vat', 10, 2)->nullable();
        });

        Schema::table('pre_order_items', function (Blueprint $table) {
            $table->decimal('unit_vat', 10, 2)->nullable();
            $table->decimal('vat_amount', 10, 2)->nullable();
            $table->decimal('vat', 10, 2)->nullable();
        });

        Schema::table('others_outlet_sale_items', function (Blueprint $table) {
            $table->decimal('unit_vat', 10, 2)->nullable();
            $table->decimal('vat_amount', 10, 2)->nullable();
            $table->decimal('vat', 10, 2)->nullable();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('taxable_amount', 10, 2)->nullable();
        });
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->decimal('taxable_amount', 10, 2)->nullable();
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->decimal('taxable_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_vat', 'vat_amount', 'vat']);
        });

        Schema::table('pre_order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_vat', 'vat_amount', 'vat']);
        });

        Schema::table('others_outlet_sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_vat', 'vat_amount', 'vat']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('taxable_amount');
        });
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->dropColumn('taxable_amount');
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->dropColumn('taxable_amount');
        });
    }
};
