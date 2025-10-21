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
                f.employee_reported_id AS reported_by_id,
                f.equipment_id,
                f.service_area_id,
                f.fault_status_id,
                f.spare_part_status_id,
                f.executor_id,
                f.completed_execution,

                -- ⭐ CAMBIO 1: AGREGAR EL CAMPO internal_code del equipo
                eq.internal_code,

                -- Nombre completo del equipo
                CONCAT(eq.brand_name, ' ', eq.vehicle_model, ' ', eq.model_year, ' ', eq.placa) AS equipment_name,

                sa.name AS service_area_name,
                fs.name AS fault_status_name,
                sps.name AS spare_part_status_name,

                CONCAT(e_reporter.first_name, ' ', e_reporter.last_name) AS reported_by_name,
                CONCAT(e_executor.first_name, ' ', e_executor.last_name) AS executor_name,

                -- CALCULA LA DURACIÓN EN DÍAS
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
