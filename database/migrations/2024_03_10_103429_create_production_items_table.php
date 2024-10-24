<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coi_id')->references('id')->on('chart_of_inventories')->onDelete('cascade');
            $table->double('rate', 16, 2)->nullable();
            $table->double('quantity', 16, 2);
            $table->foreignId('production_id')->references('id')->on('productions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_items');
    }
}
