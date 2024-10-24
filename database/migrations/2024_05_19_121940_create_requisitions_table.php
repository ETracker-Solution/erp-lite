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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->date('date')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('total_item')->nullable();
            $table->double('total_quantity', 8, 2);
            $table->text('remark')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected','cancelled'])->default('pending');
            $table->enum('type', ['FG', 'RM'])->default('FG');
            $table->foreignId('to_store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('from_store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->unsignedBigInteger('to_factory_id')->nullable();
            $table->unsignedBigInteger('from_factory_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
