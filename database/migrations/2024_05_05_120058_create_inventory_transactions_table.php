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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->date('tr_date')->comment('transaction date');
            $table->integer('tr_type')->comment('-1=out, 1=in');
            $table->double('qty', 8, 2);
            $table->double('rate', 8, 2);
            $table->double('amount', 8, 2);
            $table->string('store_id')->nullable();
            $table->string('doc_type')->nullable();
            $table->integer('doc_id')->nullable();
            $table->foreignId('coi_id')->comment('Chart Of Inventory Id')->nullable()->constrained('chart_of_inventories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
