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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('blood_group')->nullable()->change();
            $table->string('nid')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->double('salary', 8, 2)->nullable()->change();
            $table->date('joining_date')->nullable()->change();
            $table->date('confirm_date')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('blood_group')->nullable(false)->change();
            $table->string('nid')->nullable(false)->change();
            $table->date('dob')->nullable(false)->change();
            $table->double('salary', 8, 2)->nullable(false)->change();
            $table->date('joining_date')->nullable(false)->change();
            $table->date('confirm_date')->nullable(false)->change();
            $table->string('status')->nullable(false)->change();
        });
    }
};
