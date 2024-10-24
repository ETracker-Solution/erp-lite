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
        Schema::create('transfer_receive_items', function (Blueprint $table) {
            $table->id();
            $table->double('quantity', 8, 2);
            $table->double('rate', 8, 2);
            $table->foreignId('coi_id')->nullable()->constrained('chart_of_inventories')->onDelete('cascade');
            $table->foreignId('transfer_receive_id')->nullable()->constrained('transfer_receives')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_receive_items');
    }
};
