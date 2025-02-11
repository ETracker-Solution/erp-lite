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
            $table->foreignId('alter_unit_id')->nullable()->after('unit_id')->constrained('alter_units')->onDelete('set null');
            $table->double('a_unit_quantity', 16, 2)->nullable()->after('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_inventories', function (Blueprint $table) {
            $table->dropColumn('alter_unit_id');
            $table->dropColumn('a_unit_quantity');
        });
    }
};
