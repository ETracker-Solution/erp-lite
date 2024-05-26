<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('f_g_inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('date');
            $table->double('subtotal', 15, 2)->nullable();
            $table->text('remark')->nullable();
            $table->string('status')->default('pending');
            $table->enum('transaction_type',['increase','decrease'])->default('increase');
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f_g_inventory_adjustments');
    }
};
