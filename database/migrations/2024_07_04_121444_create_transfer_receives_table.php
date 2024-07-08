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
        Schema::create('transfer_receives', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('uid')->unique();
            $table->string('reference_no')->nullable();
            $table->integer('total_item')->nullable();
            $table->double('total_quantity', 8, 2);
            $table->text('remark')->nullable();
            $table->enum('status', ['received'])->default('received');
            $table->enum('type', ['FG', 'RM'])->default('FG');
            $table->foreignId('from_store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('to_store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('inventory_transfer_id')->nullable()->constrained('inventory_transfers')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_receives');
    }
};
