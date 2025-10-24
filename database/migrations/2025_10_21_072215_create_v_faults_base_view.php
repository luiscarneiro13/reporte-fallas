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
            select
                `f`.`id` AS `id`,
                `f`.`branch_id` AS `branch_id`,
                `f`.`description` AS `description`,
                `f`.`report_date` AS `report_date`,
                `f`.`closed` AS `closed_at`,
                `f`.`internal_id` AS `internal_id`,
                `f`.`scheduled_execution` AS `scheduled_execution`,
                `f`.`completed_execution` AS `completed_execution`,
                `f`.`equipment_maintenance_log` AS `equipment_maintenance_log`,
                `f`.`employee_reported_id` AS `reported_by_id`,
                concat(`e_reporter`.`identification_number`,' ',`e_reporter`.`last_name`,' ',`e_reporter`.`first_name`) AS `reported_by_name`,
                `f`.`equipment_id` AS `equipment_id`,
                concat(`eq`.`brand_name`,' ',`eq`.`vehicle_model`,' ',`eq`.`model_year`,' ',`eq`.`placa`) AS `equipment_name`,
                `eq`.`internal_code` AS `internal_code`,
                `f`.`service_area_id` AS `service_area_id`,
                `sa`.`name` AS `service_area_name`,
                `f`.`fault_status_id` AS `fault_status_id`,
                `fs`.`name` AS `fault_status_name`,
                `f`.`spare_part_status_id` AS `spare_part_status_id`,
                `sps`.`name` AS `spare_part_status_name`,
                `f`.`executor_id` AS `executor_id`,
                concat(`e_executor`.`identification_number`,' ',`e_executor`.`last_name`,' ',`e_executor`.`first_name`) AS `executor_name`,
                (to_days(if((`f`.`completed_execution` is not null),`f`.`completed_execution`,now())) - to_days(`f`.`report_date`)) AS `duration_days`,
                /* --- Nuevos Campos --- */
                `ep_latest`.`project_id` AS `project_id`,
                `proj`.`name` AS `project_name`
                /* ---------------------- */
                from
                `faults` `f`
                join `equipment` `eq` on((`f`.`equipment_id` = `eq`.`id`))
                /* --- Tablas para el Último Proyecto --- */
                left join `equipment_project` `ep_latest` on `ep_latest`.`id` = (
                    select `ep_sub`.`id`
                    from `equipment_project` `ep_sub`
                    where `ep_sub`.`equipment_id` = `eq`.`id`
                    order by `ep_sub`.`created_at` desc
                    limit 1
                )
                left join `projects` `proj` on `ep_latest`.`project_id` = `proj`.`id`
                /* -------------------------------------- */
                join `service_areas` `sa` on((`f`.`service_area_id` = `sa`.`id`))
                join `fault_statuses` `fs` on((`f`.`fault_status_id` = `fs`.`id`))
                join `spare_part_statuses` `sps` on((`f`.`spare_part_status_id` = `sps`.`id`))
                join `employees` `e_reporter` on((`f`.`employee_reported_id` = `e_reporter`.`id`))
                left join `employees` `e_executor` on((`f`.`executor_id` = `e_executor`.`id`));
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
