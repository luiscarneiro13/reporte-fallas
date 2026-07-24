<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Amplía has_driver_license / has_occupational_certificate de boolean (0/1)
        // a un tercer estado: 0 = No, 1 = Sí, 2 = N/A.
        DB::statement('ALTER TABLE `ficha_ingreso` MODIFY `has_driver_license` TINYINT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `ficha_ingreso` MODIFY `has_occupational_certificate` TINYINT UNSIGNED NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `ficha_ingreso` MODIFY `has_driver_license` TINYINT(1) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `ficha_ingreso` MODIFY `has_occupational_certificate` TINYINT(1) NOT NULL DEFAULT 0');
    }
};
