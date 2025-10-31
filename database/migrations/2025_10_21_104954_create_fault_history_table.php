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
        Schema::create('fault_history', function (Blueprint $table) {
            // METADATOS Y CLAVE PRIMARIA
            $table->id();

            // CAMPOS DE LA TABLA BASE DE LA VISTA
            // f.branch_id
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            // f.description
            $table->text('description');
            // f.report_date
            $table->date('report_date');
            // f.closed AS closed_at
            $table->date('closed_at')->nullable()->index();
            // f.internal_id
            $table->string('internal_id')->nullable()->index();
            // f.scheduled_execution
            $table->date('scheduled_execution')->nullable();
            // f.completed_execution
            $table->date('completed_execution')->nullable();
            // f.equipment_maintenance_log
            $table->text('equipment_maintenance_log')->nullable();

            // IDs ORIGINALES (f.employee_reported_id AS reported_by_id)
            $table->unsignedBigInteger('reported_by_id')->nullable();
            $table->unsignedBigInteger('equipment_id')->nullable()->index();
            $table->unsignedBigInteger('service_area_id')->nullable()->index();
            $table->unsignedBigInteger('fault_status_id')->nullable();
            $table->unsignedBigInteger('spare_part_status_id')->nullable();
            $table->unsignedBigInteger('executor_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();

            // DATOS DESNORMALIZADOS (NOMBRES/LABELS)
            $table->string('reported_by_name')->nullable();
            $table->string('equipment_name')->nullable();
            $table->string('service_area_name')->nullable();
            $table->string('fault_status_name')->nullable();
            $table->string('spare_part_status_name')->nullable();
            $table->string('executor_name')->nullable();
            $table->string('division_name')->nullable();

            // OTROS DATOS DESNORMALIZADOS
            $table->string('internal_code')->nullable();

            // ID de referencia al original (para trazabilidad)
            $table->unsignedBigInteger('original_fault_id')->unique();

            // TIMESTAMPS DE LARAVEL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fault_history');
    }
};
