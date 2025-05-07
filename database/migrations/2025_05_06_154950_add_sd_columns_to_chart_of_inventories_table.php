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
        Schema::table('chart_of_inventories', function (Blueprint $table) {
            $table->string('sd_type')->nullable();
            $table->string('sd_amount')->nullable();
            $table->string('sd')->nullable();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('unit_sd', 10, 2)->nullable();
            $table->decimal('sd_amount', 10, 2)->nullable();
            $table->decimal('sd', 10, 2)->nullable();
        });

        Schema::table('pre_order_items', function (Blueprint $table) {
            $table->decimal('unit_sd', 10, 2)->nullable();
            $table->decimal('sd_amount', 10, 2)->nullable();
            $table->decimal('sd', 10, 2)->nullable();
        });

        Schema::table('others_outlet_sale_items', function (Blueprint $table) {
            $table->decimal('unit_sd', 10, 2)->nullable();
            $table->decimal('sd_amount', 10, 2)->nullable();
            $table->decimal('sd', 10, 2)->nullable();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('sd', 10, 2)->nullable();
        });
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->decimal('sd', 10, 2)->nullable();
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->decimal('sd', 10, 2)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_inventories', function (Blueprint $table) {
            $table->dropColumn('sd_type','sd_amount','sd');
        });
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_sd', 'sd_amount', 'sd']);
        });

        Schema::table('pre_order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_sd', 'sd_amount', 'sd']);
        });

        Schema::table('others_outlet_sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_sd', 'sd_amount', 'sd']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('sd');
        });
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->dropColumn('sd');
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->dropColumn('sd');
        });
    }
};
