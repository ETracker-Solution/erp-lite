<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumptionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumption_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coi_id')->references('id')->on('chart_of_inventories')->onDelete('cascade');
            $table->double('rate', 16, 2);
            $table->double('quantity', 16, 2);
            $table->foreignId('consumption_id')->references('id')->on('consumptions')->onDelete('cascade');
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
        Schema::dropIfExists('consumption_items');
    }
}
