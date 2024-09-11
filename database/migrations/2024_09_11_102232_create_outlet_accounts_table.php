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
        Schema::create('outlet_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
            $table->foreignId('coa_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_accounts');
    }
};
