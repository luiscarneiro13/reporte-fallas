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

    static function mostFailingEquipment()
    {
        // 1. Contar las fallas activas (v_faults_base)
        $activeFaults = DB::table('v_faults_base')
            ->select('equipment_id', DB::raw('count(*) as fault_count'))
            ->whereNotNull('equipment_id')
            ->groupBy('equipment_id');

        // 2. Contar las fallas cerradas (fault_history)
        $closedFaults = DB::table('fault_history')
            ->select('equipment_id', DB::raw('count(*) as fault_count'))
            ->whereNotNull('equipment_id')
            ->groupBy('equipment_id');

        // 3. Unir los resultados y sumar los conteos por equipo
        $mostFailingEquipment = DB::query()
            ->select('equipment_id')
            ->selectRaw('SUM(fault_count) as total_faults')
            ->fromSub($activeFaults->unionAll($closedFaults), 'all_faults')
            ->groupBy('equipment_id')
            ->orderByDesc('total_faults')
            ->limit(1)
            ->get();

        // 4. Si se encontró un resultado, obtener su nombre del equipo de la vista base
        if ($mostFailingEquipment->isNotEmpty()) {
            $equipmentId = $mostFailingEquipment->first()->equipment_id;
            $equipmentInfo = DB::table('v_faults_base')
                ->where('equipment_id', $equipmentId)
                ->select('equipment_id', 'equipment_name')
                ->first();

            // Resultado final
            return [
                'equipment_id' => $equipmentId,
                'equipment_name' => $equipmentInfo->equipment_name ?? 'Nombre Desconocido',
                'total_faults' => $mostFailingEquipment->first()->total_faults
            ];
        } else {
            return null;
        }
    }

    static function mostFailReported()
    {
        // 1. Contar las fallas activas (v_faults_base)
        $activeReporters = DB::table('v_faults_base')
            ->select('reported_by_id', DB::raw('count(*) as report_count'))
            ->whereNotNull('reported_by_id')
            ->groupBy('reported_by_id');

        // 2. Contar las fallas cerradas (fault_history)
        $closedReporters = DB::table('fault_history')
            ->select('reported_by_id', DB::raw('count(*) as report_count'))
            ->whereNotNull('reported_by_id')
            ->groupBy('reported_by_id');

        // 3. Unir los resultados y sumar los conteos por reportante
        $topReporter = DB::query()
            ->select('reported_by_id')
            ->selectRaw('SUM(report_count) as total_reports')
            ->fromSub($activeReporters->unionAll($closedReporters), 'all_reporters')
            ->groupBy('reported_by_id')
            ->orderByDesc('total_reports')
            ->limit(1)
            ->get();

        // 4. Si se encontró un resultado, obtener su nombre del reportante de la vista base
        if ($topReporter->isNotEmpty()) {
            $reporterId = $topReporter->first()->reported_by_id;
            $reporterInfo = DB::table('v_faults_base')
                ->where('reported_by_id', $reporterId)
                ->select('reported_by_id', 'reported_by_name')
                ->first();

            // Resultado final
            return [
                'reported_by_id' => $reporterId,
                'reported_by_name' => $reporterInfo->reported_by_name ?? 'Nombre Desconocido',
                'total_reports' => $topReporter->first()->total_reports
            ];
        } else {
            return null;
        }
    }

    static function totalActiveFaults()
    {
        return DB::table('v_faults_base')->where('branch_id', session('branch')->id)->whereNull('closed_at')->count();
    }

    static function totalClosedFaults()
    {
        return DB::table('fault_history')->count();
    }

    static function failuresByDivision()
    {
        $failuresByDivision = DB::table('v_faults_base')
            ->select('service_area_name as division', DB::raw('COUNT(*) as total'))
            ->groupBy('service_area_name')
            ->unionAll(
                DB::table('fault_history')
                    ->select('service_area_name as division', DB::raw('COUNT(*) as total'))
                    ->groupBy('service_area_name')
            )
            ->get()
            ->groupBy('division')  // agrupa por division sumando totales
            ->map(function ($items) {
                return $items->sum('total');
            });

        $labels = $failuresByDivision->keys()->toArray();
        $values = $failuresByDivision->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
