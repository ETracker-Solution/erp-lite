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
        Schema::create('raw_material_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->integer('serial_no')->unique();
            $table->date('date');
            $table->double('qty', 8, 2);
            $table->double('rate', 8, 2);
            $table->double('amount', 8, 2);
            $table->string('store_id')->nullable();
            $table->foreignId('coi_id')->comment('Chart Of Inventory Id')->nullable()->constrained('chart_of_inventories')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_opening_balances');
    }
};
