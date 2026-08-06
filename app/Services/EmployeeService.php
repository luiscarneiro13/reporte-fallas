<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    static function totalActivePermanentEmployees()
    {
        return Employee::where('branch_id', session('branch')->id)
            ->where('external', 0)
            ->whereHas('currentEmploymentPeriod')
            ->whereHas('contractType', function ($q) {
                $q->where('name', 'Personal fijo');
            })
            ->count();
    }

    static function employeesByContractType()
    {
        $counts = Employee::where('branch_id', session('branch')->id)
            ->where('external', 0)
            ->whereHas('currentEmploymentPeriod')
            ->with('contractType')
            ->get()
            ->groupBy(function ($employee) {
                return $employee->contractType->name ?? 'Sin tipo de contrato';
            })
            ->map->count();

        return [
            'labels' => $counts->keys()->toArray(),
            'values' => $counts->values()->toArray(),
        ];
    }

    static function employeesByProject()
    {
        $counts = Employee::where('branch_id', session('branch')->id)
            ->where('external', 0)
            ->whereHas('currentEmploymentPeriod')
            ->with('projects')
            ->get()
            ->pluck('projects')
            ->flatten()
            ->countBy('name');

        return [
            'labels' => $counts->keys()->toArray(),
            'values' => $counts->values()->toArray(),
        ];
    }

    static function employeesByCargo()
    {
        $counts = Employee::where('branch_id', session('branch')->id)
            ->where('external', 0)
            ->whereHas('currentEmploymentPeriod')
            ->with('cargo')
            ->get()
            ->groupBy(function ($employee) {
                return $employee->cargo->name ?? 'Sin cargo';
            })
            ->map->count();

        return [
            'labels' => $counts->keys()->toArray(),
            'values' => $counts->values()->toArray(),
        ];
    }
}
