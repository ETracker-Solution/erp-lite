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
            $table->string('delivery_type')->nullable();
            $table->string('delivery_area')->nullable();
        });
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->string('delivery_type')->nullable();
            $table->string('delivery_area')->nullable();
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->string('delivery_type')->nullable();
            $table->string('delivery_area')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
            $table->dropColumn('delivery_area');
        });
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
            $table->dropColumn('delivery_area');
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
            $table->dropColumn('delivery_area');
        });
    }
};
