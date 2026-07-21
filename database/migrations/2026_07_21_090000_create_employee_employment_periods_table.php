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
        Schema::create('employee_employment_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('contract_type_id')->nullable()->constrained('contract_types')->nullOnDelete();
            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('termination_reason', 150)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Crea el período inicial de cada empleado existente a partir de los
        // datos actuales de "employees" (hire_date/contract_type_id/branch_id/cargo_id),
        // dejando end_date en null porque hoy no hay forma de saber si ya egresaron.
        $employees = DB::table('employees')->select(
            'id',
            'branch_id',
            'contract_type_id',
            'cargo_id',
            'hire_date'
        )->get();

        foreach ($employees as $employee) {
            DB::table('employee_employment_periods')->insert([
                'employee_id' => $employee->id,
                'branch_id' => $employee->branch_id,
                'contract_type_id' => $employee->contract_type_id,
                'cargo_id' => $employee->cargo_id,
                'start_date' => $employee->hire_date ?? now()->toDateString(),
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_employment_periods');
    }
};
