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
        Schema::create('requisition_deliveries', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('uid')->unique();
            $table->string('reference_no')->nullable();
            $table->integer('total_item')->nullable();
            $table->double('total_quantity', 8, 2);
            $table->text('remark')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected','received'])->default('pending');
            $table->enum('type', ['FG', 'RM'])->default('FG');
            $table->foreignId('from_store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('to_store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('requisition_id')->nullable()->constrained('requisitions')->onDelete('cascade');
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
        Schema::dropIfExists('requisition_deliveries');
    }
};
