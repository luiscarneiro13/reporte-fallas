<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\FaultStatus;
use App\Models\Project;
use App\Models\ServiceArea;
use App\Models\SparePartStatus;
use Illuminate\Support\Facades\DB;

class FaultService
{

    static function employeeReported()
    {
        $employees = Employee::where('branch_id', session('branch')->id)
            ->where('external', 0)
            ->select(
                'id',
                DB::raw("CONCAT(identification_number, ' - ', last_name, ' ', first_name) AS full_name")
            )
            ->orderBy('last_name', 'asc')
            ->pluck('full_name', 'id');
        return $employees;
    }

    static function equipment()
    {
        $equipment = Equipment::where('branch_id', session('branch')->id)
            ->select(
                'id',
                DB::raw("CONCAT(placa, ' - ', vehicle_model) AS full_placa")
            )
            ->orderBy('vehicle_model', 'asc')
            ->pluck('full_placa', 'id');

        return $equipment;
    }

    static function serviceArea()
    {
        $serviceArea = ServiceArea::where('branch_id', session('branch')->id)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return $serviceArea;
    }

    static function faultStatus()
    {
        $faultStatuses = FaultStatus::where('branch_id', session('branch')->id)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return $faultStatuses;
    }

    static function sparePartStatuses()
    {
        $sparePartStatuses = SparePartStatus::where('branch_id', session('branch')->id)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return $sparePartStatuses;
    }

    static function executors()
    {
        $initValue = collect(["0" => "N/A"]);

        $employees = Employee::where('branch_id', session('branch')->id)
            ->where('executor', 1)
            ->select(
                'id',
                DB::raw("CONCAT(identification_number, ' - ', last_name, ' ', first_name) AS full_name")
            )
            ->orderBy('last_name', 'asc')
            ->pluck('full_name', 'id');

        $result = $initValue->merge($employees);

        return $result;
    }

    static function projects()
    {
        $projects = Project::where('branch_id', session('branch')->id)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return $projects;
    }
}
