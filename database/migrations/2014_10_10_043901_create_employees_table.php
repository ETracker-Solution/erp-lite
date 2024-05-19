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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('nominee_name')->nullable();
            $table->string('nominee_relation')->nullable();
            $table->string('blood_group');
            $table->string('nid');
            $table->date('dob');
            $table->string('bank_account')->nullable();
            $table->string('present_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('email');
            $table->string('personal_email')->nullable();
            $table->string('phone');
            $table->string('alternative_phone')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('designation_id')->nullable()->constrained('designations')->onDelete('set null');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('set null');
            $table->double('sallery', 8, 2);
            $table->date('joining_date');
            $table->date('confirm_date');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
