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
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->nullable()->after('vat');
            $table->foreignId('alter_unit_id')->nullable()->constrained('alter_units')->onDelete('set null');
            $table->double('a_unit_quantity', 16, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->dropColumn('discount');
                $table->dropColumn('alter_unit_id');
                $table->dropColumn('a_unit_quantity');
            });
        });
    }
};
