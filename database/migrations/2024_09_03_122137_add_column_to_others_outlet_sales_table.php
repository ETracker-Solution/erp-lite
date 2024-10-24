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
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->time('delivery_time')->nullable();
            $table->double('delivery_charge', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->dropColumn('delivery_time');
            $table->dropColumn('delivery_charge');
        });
    }
};
