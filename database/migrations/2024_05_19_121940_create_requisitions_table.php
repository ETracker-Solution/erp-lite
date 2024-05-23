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
            $table->date('date')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('total_item')->nullable();
            $table->string('uid')->nullable();
            $table->double('total_quantity', 8, 2);
            $table->double('subtotal', 8, 2)->default(0);
            $table->text('remark')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])->default('pending');
            $table->enum('type', ['FG', 'RM'])->default('FG');
            $table->string('created_by')->nullable();
            $table->string('approved_by')->nullable();
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
