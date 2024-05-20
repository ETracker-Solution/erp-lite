<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_points', function (Blueprint $table) {
            $table->id();
            $table->double('from_amount', 16, 2)->nullable();
            $table->double('to_amount', 16, 2)->nullable();
            $table->double('per_amount', 16, 2)->nullable();
            $table->double('point', 8, 2);
            $table->foreignId('member_type_id')->nullable()->constrained('member_types')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('member_points');
    }
}
