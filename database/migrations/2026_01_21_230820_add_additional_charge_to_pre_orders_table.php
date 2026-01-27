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
            if (!Schema::hasColumn('pre_orders', 'delivery_charge')) {
                $table->double('delivery_charge', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('pre_orders', 'additional_charge')) {
                $table->double('additional_charge', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            if (Schema::hasColumn('pre_orders', 'delivery_charge')) {
                $table->dropColumn('delivery_charge');
            }

            if (Schema::hasColumn('pre_orders', 'additional_charge')) {
                $table->dropColumn('additional_charge');
            }
        });
    }
};
