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
            $table->string('vat_type')->nullable()->after('status');
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_inventories', function (Blueprint $table) {
            $table->dropColumn(['vat_type', 'vat_amount', 'vat', 'base_price', 'total_price']);
        });
    }
};
