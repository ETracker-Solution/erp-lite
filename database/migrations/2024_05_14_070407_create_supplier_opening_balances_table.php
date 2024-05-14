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
        Schema::create('supplier_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->date('date');
            $table->double('amount', 8, 2);
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('coia_id')->comment('Chart Of Account Id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');
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
        Schema::dropIfExists('supplier_opening_balances');
    }
};
