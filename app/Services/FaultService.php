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
            ->where('branch_id', session('branch')->id)->whereNull('closed_at')
            ->whereNotNull('equipment_id')
            ->groupBy('equipment_id');

        // 2. Contar las fallas cerradas (fault_history)
        $closedFaults = DB::table('fault_history')
            ->select('equipment_id', DB::raw('count(*) as fault_count'))
            ->where('branch_id', session('branch')->id)
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
                ->where('branch_id', session('branch')->id)->whereNull('closed_at')
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
            ->where('branch_id', session('branch')->id)
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
        return DB::table('fault_history')->where('branch_id', session('branch')->id)->count();
    }

    static function failuresByDivision()
    {
        $failuresByDivision = DB::table('v_faults_base')
            ->select('service_area_name as division', DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at')
            ->groupBy('service_area_name')
            ->unionAll(
                DB::table('fault_history')
                    ->select('service_area_name as division', DB::raw('COUNT(*) as total'))
                    ->where('branch_id', session('branch')->id)
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

    static function failuresByProject()
    {
        $failuresByProject = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(project_name, "Sin proyecto") as project'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at')
            ->groupBy('project_name')
            ->get()
            ->pluck('total', 'project'); // devuelve ['Proyecto A' => 10, 'Proyecto B' => 5, ...]

        $labels = $failuresByProject->keys()->toArray();
        $values = $failuresByProject->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresByReporter()
    {
        $failuresByReporter = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(reported_by_name, "Sin reportante") as reporter'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at')
            ->groupBy('reported_by_name')
            ->get()
            ->pluck('total', 'reporter'); // devuelve ['Juan Pérez' => 10, 'Ana Gómez' => 5, ...]

        $labels = $failuresByReporter->keys()->toArray();
        $values = $failuresByReporter->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresByEquipment()
    {
        $failuresByEquipment = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(equipment_name, "Sin equipo") as equipment'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at')
            ->groupBy('equipment_name')
            ->get()
            ->pluck('total', 'equipment'); // devuelve ['Equipo A' => 12, 'Equipo B' => 7, ...]

        $labels = $failuresByEquipment->keys()->toArray();
        $values = $failuresByEquipment->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresByStatus()
    {
        $failuresByStatus = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(fault_status_name, "Sin estatus") as status'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at')
            ->groupBy('fault_status_name')
            ->get()
            ->pluck('total', 'status'); // devuelve ['Abierta' => 10, 'Cerrada' => 7, ...]

        $labels = $failuresByStatus->keys()->toArray();
        $values = $failuresByStatus->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresBySparePartStatus()
    {
        $failuresBySparePartStatus = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(spare_part_status_name, "Sin estatus de repuesto") as status'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at')
            ->groupBy('spare_part_status_name')
            ->get()
            ->pluck('total', 'status'); // devuelve ['Disponible' => 10, 'No disponible' => 5, ...]

        $labels = $failuresBySparePartStatus->keys()->toArray();
        $values = $failuresBySparePartStatus->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function faultsByStatus()
    {
        $totalActive = DB::table('v_faults_base')
            ->where('branch_id', session('branch')->id)
            ->whereNull('closed_at')
            ->count();

        $totalClosed = DB::table('fault_history')
            ->where('branch_id', session('branch')->id)
            ->count();

        $labels = ['Fallas Activas', 'Fallas Cerradas'];
        $values = [$totalActive, $totalClosed];

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
