<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('cargo_id')->nullable()->after('position')
                ->constrained('cargos')->nullOnDelete();
        });

        // Migra los valores de texto libre existentes en "position" hacia el
        // catálogo "cargos" (por sucursal) y enlaza cada empleado con su cargo.
        $employees = DB::table('employees')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->select('id', 'branch_id', 'position')
            ->get();

        $cache = [];

        foreach ($employees as $employee) {
            $cacheKey = $employee->branch_id . '|' . $employee->position;

            if (!isset($cache[$cacheKey])) {
                $cargoId = DB::table('cargos')
                    ->where('branch_id', $employee->branch_id)
                    ->where('name', $employee->position)
                    ->value('id');

                if (!$cargoId) {
                    $cargoId = DB::table('cargos')->insertGetId([
                        'branch_id' => $employee->branch_id,
                        'name' => $employee->position,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $cache[$cacheKey] = $cargoId;
            }

            DB::table('employees')
                ->where('id', $employee->id)
                ->update(['cargo_id' => $cache[$cacheKey]]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['cargo_id']);
            $table->dropColumn('cargo_id');
        });
    }
};
