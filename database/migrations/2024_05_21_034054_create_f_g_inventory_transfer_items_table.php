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
        Schema::create('f_g_inventory_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coi_id')->references('id')->on('chart_of_inventories')->onDelete('cascade');
            $table->double('rate', 16, 2);
            $table->double('quantity', 16, 2);
            $table->foreignId('f_g_inventory_transfer_id')->references('id')->on('f_g_inventory_transfers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f_g_inventory_transfer_items');
    }
};
