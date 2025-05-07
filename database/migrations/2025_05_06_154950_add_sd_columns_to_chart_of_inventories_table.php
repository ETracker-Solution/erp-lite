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
            $table->boolean('is_vat')->default(false);
        });

        Schema::table('pre_order_items', function (Blueprint $table) {
            $table->decimal('unit_sd', 10, 2)->nullable();
            $table->decimal('sd_amount', 10, 2)->nullable();
            $table->decimal('sd', 10, 2)->nullable();
            $table->boolean('is_vat')->default(false);
        });

        Schema::table('others_outlet_sale_items', function (Blueprint $table) {
            $table->decimal('unit_sd', 10, 2)->nullable();
            $table->decimal('sd_amount', 10, 2)->nullable();
            $table->decimal('sd', 10, 2)->nullable();
            $table->boolean('is_vat')->default(false);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('sd', 10, 2)->nullable();
            $table->boolean('is_vat')->default(false);
        });
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->decimal('sd', 10, 2)->nullable();
            $table->boolean('is_vat')->default(false);
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->decimal('sd', 10, 2)->nullable();
            $table->boolean('is_vat')->default(false);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_inventories', function (Blueprint $table) {
            $table->dropColumn('sd_type','sd_amount','sd','is_vat');
        });
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_sd', 'sd_amount', 'sd','is_vat']);
        });

        Schema::table('pre_order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_sd', 'sd_amount', 'sd','is_vat']);
        });

        Schema::table('others_outlet_sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_sd', 'sd_amount', 'sd','is_vat']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('sd','is_vat');
        });
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->dropColumn('sd','is_vat');
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->dropColumn('sd','is_vat');
        });
    }
};
