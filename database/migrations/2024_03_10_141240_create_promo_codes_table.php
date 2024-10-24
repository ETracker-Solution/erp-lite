<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('discount_type');
            $table->string('discount_value');
            $table->unsignedFloat('minimum_purchase')->default(0);
            $table->enum('status',['active','inactive','expired'])->default('active');
            $table->string('discount_for')->nullable();
            $table->string('member_types')->nullable();
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
        Schema::dropIfExists('promo_codes');
    }
}
