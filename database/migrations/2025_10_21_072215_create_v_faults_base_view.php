<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = "
            CREATE OR REPLACE VIEW v_faults_base AS
            SELECT
            f.id,
            f.branch_id,
            f.description,
            f.report_date,
            f.closed AS closed_at,
            f.internal_id,
            f.scheduled_execution,
            f.completed_execution,

            -- CAMPO DE TEXTO DE CIERRE
            f.equipment_maintenance_log,

            -- IDS Y NOMBRES DESNORMALIZADOS JUNTOS
            f.employee_reported_id AS reported_by_id,
            -- Empleado que Reporta: identification_number last_name first_name
            CONCAT(e_reporter.identification_number, ' ', e_reporter.last_name, ' ', e_reporter.first_name) AS reported_by_name,

            f.equipment_id,
            CONCAT(eq.brand_name, ' ', eq.vehicle_model, ' ', eq.model_year, ' ', eq.placa) AS equipment_name,

            f.service_area_id,
            sa.name AS service_area_name,

            f.fault_status_id,
            fs.name AS fault_status_name,

            f.spare_part_status_id,
            sps.name AS spare_part_status_name,

            f.executor_id,
            -- Ejecutor: identification_number last_name first_name
            CONCAT(e_executor.identification_number, ' ', e_executor.last_name, ' ', e_executor.first_name) AS executor_name,

            -- OTROS DATOS DESNORMALIZADOS
            eq.internal_code,

            -- CÁLCULO DE DURACIÓN
            DATEDIFF(
                IF(f.completed_execution IS NOT NULL, f.completed_execution, NOW()),
                f.report_date
            ) AS duration_days

        FROM
            faults f
        INNER JOIN
            equipment eq ON f.equipment_id = eq.id
        INNER JOIN
            service_areas sa ON f.service_area_id = sa.id
        INNER JOIN
            fault_statuses fs ON f.fault_status_id = fs.id
        INNER JOIN
            spare_part_statuses sps ON f.spare_part_status_id = sps.id

        INNER JOIN
            employees e_reporter ON f.employee_reported_id = e_reporter.id
        LEFT JOIN
            employees e_executor ON f.executor_id = e_executor.id
        ";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_faults_base");
    }
};
