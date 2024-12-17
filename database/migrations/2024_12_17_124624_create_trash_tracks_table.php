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
        Schema::create('trash_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->json('data');
            $table->ipAddress();
            $table->text('user_agent')->nullable();
            $table->string('user_name');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trash_tracks');
    }
};
