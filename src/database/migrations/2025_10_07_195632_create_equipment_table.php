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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('branch_id');
            $table->string('placa', 20)->index();
            $table->string('serial_niv', 90)->nullable()->index();
            $table->string('body_serial_number', 90)->nullable()->index();
            $table->string('chassis_serial_number', 90)->nullable()->index();
            $table->string('engine_serial_number', 90)->nullable()->index();
            $table->string('vehicle_model', 90)->nullable()->index();
            $table->string('brand_name', 90)->nullable()->index();
            $table->string('owner', 20)->nullable()->index();
            $table->string('model_year', 20)->nullable()->index();
            $table->string('internal_code', 20)->nullable()->index();
            $table->string('color', 20)->nullable()->nullable();
            $table->string('origin')->nullable()->nullable();
            $table->string('racda', 10)->nullable();

            $table->timestamps();
            $table->foreign('branch_id')->references('id')->on('branches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
