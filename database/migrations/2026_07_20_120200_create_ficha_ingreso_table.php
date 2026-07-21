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
        Schema::create('ficha_ingreso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained('employees')->cascadeOnDelete();

            $table->string('photo')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality', 90)->nullable();

            $table->boolean('has_driver_license')->default(false);
            $table->string('driver_license_grade', 20)->nullable();

            $table->string('account_number', 40)->nullable();
            $table->string('account_type', 20)->nullable();
            $table->string('bank', 90)->nullable();

            $table->boolean('has_occupational_certificate')->default(false);

            $table->string('shirt_size', 10)->nullable();
            $table->string('coverall_size', 10)->nullable();
            $table->string('shoe_size', 10)->nullable();

            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficha_ingreso');
    }
};
