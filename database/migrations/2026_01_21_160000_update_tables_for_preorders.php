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
        // Add pre_order_id to sales table
        if (!Schema::hasColumn('sales', 'pre_order_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->foreignId('pre_order_id')->nullable()->constrained('pre_orders')->nullOnDelete();
            });
        }

        // Create pre_order_transactions table
        Schema::create('pre_order_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_order_id')->constrained('pre_orders')->cascadeOnDelete();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts'); // Account credited (Advance Collection) or debited (Cash)
            $table->double('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('transaction_type'); // 'debit' or 'credit'
            $table->date('date');
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_order_transactions');

        if (Schema::hasColumn('sales', 'pre_order_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropForeign(['pre_order_id']);
                $table->dropColumn('pre_order_id');
            });
        }
    }
};
