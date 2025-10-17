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
        Schema::create('faults', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('internal_id')->nullable()->index();
            $table->unsignedBigInteger('employee_reported_id')->nullable();
            $table->unsignedBigInteger('equipment_id')->nullable();
            $table->unsignedBigInteger('service_area_id')->nullable();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('fault_status_id')->nullable();
            $table->unsignedBigInteger('spare_part_status_id')->nullable();
            $table->date('report_date')->nullable()->index();
            $table->date('scheduled_execution')->nullable()->index();
            $table->date('completed_execution')->nullable()->index();

            $table->unsignedBigInteger('executor_id')->nullable();
            $table->text('equipment_maintenance_log')->nullable();

            $table->date('closed')->nullable()->index();

            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('employee_reported_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->foreign('fault_status_id')->references('id')->on('fault_statuses')->onDelete('cascade');
            $table->foreign('spare_part_status_id')->references('id')->on('spare_part_statuses')->onDelete('cascade');
            $table->foreign('service_area_id')->references('id')->on('service_areas')->onDelete('cascade');
            $table->foreign('executor_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faults');
    }
};
