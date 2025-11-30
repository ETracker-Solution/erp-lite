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
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->integer('max_use_per_user')->default(1);
        });
        Schema::table('customer_promo_codes', function (Blueprint $table) {
            $table->integer('max_use')->default(1);
            $table->integer('already_used')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn(['max_use_per_user']);
        });
        Schema::table('customer_promo_codes', function (Blueprint $table) {
            $table->dropColumn(['max_use','already_used']);
        });
    }
};
