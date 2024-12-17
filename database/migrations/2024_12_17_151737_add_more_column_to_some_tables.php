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
            $table->double('additional_charge', 15, 2)->default(0);
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->double('additional_charge', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('additional_charge');
        });
        Schema::table('others_outlet_sales', function (Blueprint $table) {
            $table->dropColumn('additional_charge');
        });
    }
};
