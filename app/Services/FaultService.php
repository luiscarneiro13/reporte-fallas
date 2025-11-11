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
                DB::raw("CONCAT_WS(' - ',type, internal_code, placa, vehicle_model) AS full_placa")
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

    static function mostFailingEquipment($from = null, $to = null)
    {
        // 1. Contar las fallas activas (v_faults_base)
        $activeFaults = DB::table('v_faults_base')
            ->select('equipment_id', DB::raw('count(*) as fault_count'))
            ->where('branch_id', session('branch')->id)
            ->whereNull('closed_at')
            ->whereNotNull('equipment_id');

        // 2. Contar las fallas cerradas (fault_history)
        $closedFaults = DB::table('fault_history')
            ->select('equipment_id', DB::raw('count(*) as fault_count'))
            ->where('branch_id', session('branch')->id)
            ->whereNotNull('equipment_id');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $activeFaults->whereBetween('report_date', [$from, $to]);
            $closedFaults->whereBetween('report_date', [$from, $to]);
        }

        // Agrupamos en las subconsultas para evitar el error ONLY_FULL_GROUP_BY
        $activeFaults->groupBy('equipment_id');
        $closedFaults->groupBy('equipment_id');

        // 3. Unir los resultados y sumar los conteos por equipo
        $mostFailingEquipment = DB::query()
            ->select('equipment_id')
            ->selectRaw('SUM(fault_count) as total_faults')
            ->fromSub($activeFaults->unionAll($closedFaults), 'all_faults')
            ->groupBy('equipment_id') // Re-agrupamos el resultado del UNION
            ->orderByDesc('total_faults')
            ->limit(1)
            ->get();

        // 4. Si se encontró un resultado, obtener su nombre del equipo de la vista base
        if ($mostFailingEquipment->isNotEmpty()) {
            $equipmentId = $mostFailingEquipment->first()->equipment_id;
            // La información del equipo se busca sin filtro de fecha, ya que es estática.
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

    static function mostFailReported($from = null, $to = null)
    {
        // 1. Contar las fallas activas (v_faults_base)
        $activeReporters = DB::table('v_faults_base')
            ->select('reported_by_id', DB::raw('count(*) as report_count'))
            ->where('branch_id', session('branch')->id)
            ->whereNotNull('reported_by_id');

        // 2. Contar las fallas cerradas (fault_history)
        $closedReporters = DB::table('fault_history')
            ->select('reported_by_id', DB::raw('count(*) as report_count'))
            ->where('branch_id', session('branch')->id)
            ->whereNotNull('reported_by_id');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $activeReporters->whereBetween('report_date', [$from, $to]);
            $closedReporters->whereBetween('report_date', [$from, $to]);
        }

        // Agrupar aquí, antes del UNION ALL.
        $activeReporters->groupBy('reported_by_id');
        $closedReporters->groupBy('reported_by_id');

        // 3. Unir los resultados y sumar los conteos por reportante
        $topReporter = DB::query()
            ->select('reported_by_id')
            ->selectRaw('SUM(report_count) as total_reports')
            ->fromSub($activeReporters->unionAll($closedReporters), 'all_reporters')
            ->groupBy('reported_by_id')
            ->orderByDesc('total_reports')
            ->limit(1)
            ->get();

        // 4. Si se encontró un resultado, obtener su nombre del reportante
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

    static function totalActiveFaults($from = null, $to = null)
    {
        $query = DB::table('v_faults_base')->where('branch_id', session('branch')->id)->whereNull('closed_at');

        if ($from && $to) {
            $query->whereBetween('report_date', [$from, $to]);
        }

        return $query->count();
    }

    static function totalClosedFaults($from = null, $to = null)
    {
        $query = DB::table('fault_history')->where('branch_id', session('branch')->id);

        if ($from && $to) {
            $query->whereBetween('report_date', [$from, $to]);
        }

        return $query->count();
    }

    static function failuresByServiceArea($from = null, $to = null)
    {
        // Fallas activas
        $activeFaults = DB::table('v_faults_base')
            ->select('service_area_name', DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at')
            ->groupBy('service_area_name');

        // Fallas cerradas
        $closedFaults = DB::table('fault_history')
            ->select('service_area_name', DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)
            ->groupBy('service_area_name');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $activeFaults->whereBetween('report_date', [$from, $to]);
            $closedFaults->whereBetween('report_date', [$from, $to]);
        }

        // Unir y sumar
        $failuresByDivision = $activeFaults->unionAll($closedFaults)
            ->get()
            ->groupBy('service_area_name')
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

    static function failuresByProject($from = null, $to = null)
    {
        $query = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(project_name, "Sin proyecto") as project'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $query->whereBetween('report_date', [$from, $to]);
        }

        $failuresByProject = $query->groupBy('project')
            ->get()
            ->pluck('total', 'project');

        $labels = $failuresByProject->keys()->toArray();
        $values = $failuresByProject->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresByReporter($from = null, $to = null)
    {
        $query = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(reported_by_name, "Sin reportante") as reporter'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $query->whereBetween('report_date', [$from, $to]);
        }

        $failuresByReporter = $query->groupBy('reporter')
            ->get()
            ->pluck('total', 'reporter');

        $labels = $failuresByReporter->keys()->toArray();
        $values = $failuresByReporter->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresByEquipment($from = null, $to = null)
    {
        $query = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(equipment_name, "Sin equipo") as equipment'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $query->whereBetween('report_date', [$from, $to]);
        }

        $failuresByEquipment = $query->groupBy('equipment')
            ->get()
            ->pluck('total', 'equipment');

        $labels = $failuresByEquipment->keys()->toArray();
        $values = $failuresByEquipment->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresByStatus($from = null, $to = null)
    {
        $query = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(fault_status_name, "Sin estatus") as status'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $query->whereBetween('report_date', [$from, $to]);
        }

        $failuresByStatus = $query->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $labels = $failuresByStatus->keys()->toArray();
        $values = $failuresByStatus->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresBySparePartStatus($from = null, $to = null)
    {
        $query = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(spare_part_status_name, "Sin estatus de repuesto") as status'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)->whereNull('closed_at');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $query->whereBetween('report_date', [$from, $to]);
        }

        $failuresBySparePartStatus = $query->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $labels = $failuresBySparePartStatus->keys()->toArray();
        $values = $failuresBySparePartStatus->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function faultsByStatus($from = null, $to = null)
    {
        $activeQuery = DB::table('v_faults_base')
            ->where('branch_id', session('branch')->id)
            ->whereNull('closed_at');

        $closedQuery = DB::table('fault_history')
            ->where('branch_id', session('branch')->id);

        if ($from && $to) {
            $activeQuery->whereBetween('report_date', [$from, $to]);
            $closedQuery->whereBetween('report_date', [$from, $to]);
        }

        $totalActive = $activeQuery->count();
        $totalClosed = $closedQuery->count();

        $labels = ['Fallas Activas', 'Fallas Cerradas'];
        $values = [$totalActive, $totalClosed];

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    static function failuresByDivision($from = null, $to = null)
    {
        // --- A. Fallas Activas (v_faults_base) ---
        // 🟢 AHORA SE TOMA division_name DIRECTAMENTE DE LA VISTA
        $activeFaults = DB::table('v_faults_base')
            ->select(DB::raw('COALESCE(division_name, "Sin División") as division_name'), DB::raw('COUNT(v_faults_base.id) as total'))
            // 🔴 Se eliminaron los JOINs a projects y divisions

            ->where('v_faults_base.branch_id', session('branch')->id)
            ->whereNull('v_faults_base.closed_at')
            ->groupBy('division_name');

        // --- B. Fallas Cerradas (fault_history) ---
        // Este query se mantiene igual, ya que usa el campo desnormalizado 'division_name' del historial.
        $closedFaults = DB::table('fault_history')
            ->select(DB::raw('COALESCE(division_name, "Sin División") as division_name'), DB::raw('COUNT(*) as total'))
            ->where('branch_id', session('branch')->id)
            ->groupBy('division_name');

        // APLICAR FILTRO DE FECHAS
        if ($from && $to) {
            $activeFaults->whereBetween('report_date', [$from, $to]);
            // Asumimos que fault_history usa report_date para las fallas cerradas también.
            $closedFaults->whereBetween('report_date', [$from, $to]);
        }

        // 3. Unir los Query Builders y sumar los totales
        $unionQuery = $activeFaults->unionAll($closedFaults);

        // La consulta externa agrupa el resultado del UNION.
        $failuresByDivision = DB::query()
            ->select('division_name')
            ->selectRaw('SUM(total) as total_failures')
            ->fromSub($unionQuery, 'all_failures')
            ->groupBy('division_name')
            ->orderByDesc('total_failures')
            ->get()
            ->pluck('total_failures', 'division_name');

        $labels = $failuresByDivision->keys()->toArray();
        $values = $failuresByDivision->values()->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
