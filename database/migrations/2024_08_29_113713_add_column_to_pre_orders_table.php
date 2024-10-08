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
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->string('size')->nullable();
            $table->string('flavour')->nullable();
            $table->string('cake_message')->nullable();
            $table->time('delivery_time')->nullable();
            $table->double('delivery_charge', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->dropColumn('size');
            $table->dropColumn('flavour');
            $table->dropColumn('cake_message');
            $table->dropColumn('delivery_time');
            $table->dropColumn('delivery_charge');
        });
    }
};
