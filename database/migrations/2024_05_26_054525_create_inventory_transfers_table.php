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
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('date');
            $table->double('subtotal', 15, 2)->nullable();
            $table->text('remark')->nullable();
            $table->enum('type', ['FG', 'RM'])->default('FG');
            $table->string('status')->default('pending');
            $table->foreignId('from_store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('to_store_id')->nullable()->constrained('stores')->onDelete('cascade');
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
        Schema::dropIfExists('inventory_transfers');
    }
};
