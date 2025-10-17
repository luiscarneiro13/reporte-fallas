<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Fault;
use App\Models\FaultStatus;
use App\Models\ServiceArea;
use App\Models\SparePartStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();
        $employeeReported = Employee::where('branch_id', $branch->id)->first();
        $equipment = Equipment::where('branch_id', $branch->id)->first();
        $serviceArea = ServiceArea::where('branch_id', $branch->id)->first();
        $faultStatus = FaultStatus::where('branch_id', $branch->id)->first();
        $sparePartStatus = SparePartStatus::where('branch_id', $branch->id)->first();

        $faults = [
            [
                'branch_id'            => $branch->id,
                'employee_reported_id' => $employeeReported->id,
                'equipment_id'         => $equipment->id,
                'service_area_id'      => $serviceArea->id,
                'description'          => 'The engine is not starting or has stopped working.',
                'fault_status_id'      => $faultStatus->id,
                'spare_part_status_id' => $sparePartStatus->id,
                'report_date'        => now(),
            ],
            [
                'branch_id'            => $branch->id,
                'employee_reported_id' => $employeeReported->id,
                'equipment_id'         => $equipment->id,
                'service_area_id'      => $serviceArea->id,
                'description'          => 'Problems with the braking system, including reduced effectiveness or failure.',
                'fault_status_id'      => $faultStatus->id,
                'spare_part_status_id' => $sparePartStatus->id,
                'report_date'        => now(),
            ],
            [
                'branch_id'            => $branch->id,
                'employee_reported_id' => $employeeReported->id,
                'equipment_id'         => $equipment->id,
                'service_area_id'      => $serviceArea->id,
                'description'          => "Issues with the vehicle\'s electrical system, such as battery or wiring faults.",
                'fault_status_id'      => $faultStatus->id,
                'spare_part_status_id' => $sparePartStatus->id,
                'report_date'        => now(),
            ],
            [
                'branch_id'            => $branch->id,
                'employee_reported_id' => $employeeReported->id,
                'equipment_id'         => $equipment->id,
                'service_area_id'      => $serviceArea->id,
                'description'          => 'Difficulties in shifting gears or transmission slipping.',
                'fault_status_id'      => $faultStatus->id,
                'spare_part_status_id' => $sparePartStatus->id,
                'report_date'        => now(),
            ],
            [
                'branch_id'            => $branch->id,
                'employee_reported_id' => $employeeReported->id,
                'equipment_id'         => $equipment->id,
                'service_area_id'      => $serviceArea->id,
                'description'          => "Issues with the vehicle\'s suspension system, leading to poor ride quality or handling.",
                'fault_status_id'      => $faultStatus->id,
                'spare_part_status_id' => $sparePartStatus->id,
                'report_date'        => now(),
            ],
        ];

        foreach ($faults as $fault) {
            Fault::firstOrCreate(
                ['description' => $fault['description']],
                $fault
            );
        }
    }
}
