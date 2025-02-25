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
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->integer('alter_unit_id')->nullable();
            $table->double('a_unit_quantity', 16, 2)->nullable();
            $table->double('alt_unit_rate', 16, 2)->nullable();
            $table->double('value_amount', 16, 2)->nullable();
            $table->double('unit_qty', 16, 2)->nullable();
            $table->string('converted_unit_qty')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn('alter_unit_id');
            $table->dropColumn('a_unit_quantity');
            $table->dropColumn('alt_unit_rate');
            $table->dropColumn('value_amount');
            $table->dropColumn('unit_qty');
            $table->dropColumn('converted_unit_qty');
        });
    }
};
