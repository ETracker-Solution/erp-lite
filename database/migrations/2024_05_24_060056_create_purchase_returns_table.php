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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->string('reference_no')->nullable();
            $table->enum('status', ['pending', 'received', 'ordered'])->default('received');
            $table->date('date')->nullable();
            $table->double('subtotal', 15, 2);
            $table->double('vat', 15, 2)->default(0);
            $table->double('grand_total', 15, 2);
            $table->double('net_payable', 15, 2);
            $table->text('remark')->nullable();
            $table->foreignId('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreignId('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreignId('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_returns');
    }
};
