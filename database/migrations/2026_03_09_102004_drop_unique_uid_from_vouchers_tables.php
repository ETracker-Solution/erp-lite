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
        Schema::table('supplier_payment_vouchers', function (Blueprint $table) {
            $table->dropUnique('supplier_payment_vouchers_uid_unique');
        });

        Schema::table('customer_receive_vouchers', function (Blueprint $table) {
            $table->dropUnique('customer_receive_vouchers_uid_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_payment_vouchers', function (Blueprint $table) {
            $table->unique('uid');
        });

        Schema::table('customer_receive_vouchers', function (Blueprint $table) {
            $table->unique('uid');
        });

    }
};
