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
        Schema::create('others_outlet_sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 20)->nullable();
            $table->string('invoice_number', 20);
            $table->double('subtotal', 15, 2);
            $table->double('discount', 8, 2)->default(0);
            $table->double('vat', 8, 2)->nullable();
            $table->double('grand_total', 15, 2);
            $table->double('receive_amount', 15, 2)->default(0);
            $table->double('delivery_point_receive_amount', 15, 2)->default(0);
            $table->double('change_amount', 15, 2)->default(0);
            $table->date('date')->nullable();
            $table->string('status')->nullable()->default('final');
            $table->string('payment_status')->nullable()->default('due');
            $table->text('remark')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('cascade');
            $table->foreignId('delivery_point_id')->nullable()->constrained('outlets')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->json('payment_methods')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('others_outlet_sales');
    }
};
