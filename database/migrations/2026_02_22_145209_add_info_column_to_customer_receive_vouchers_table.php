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
        Schema::table('customer_receive_vouchers', function (Blueprint $table) {
            $table->double('settle_discount')->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_receive_vouchers', function (Blueprint $table) {
            $table->dropColumn('settle_discount');
        });
    }
};
