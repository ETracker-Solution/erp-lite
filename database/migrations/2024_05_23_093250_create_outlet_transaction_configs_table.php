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
        Schema::create('outlet_transaction_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('outlet_id');
            $table->unsignedInteger('coa_id')->comment('chart of account id');
            $table->string('type');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_transaction_configs');
    }
};
