<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Models\FaultHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FaultHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        // 1. Inicializar la consulta base
        $historyQuery = FaultHistory::query()
            // Filtro obligatorio: Solo historial de la sucursal actual
            ->where('branch_id', session('branch')->id);

        // 2. Aplicar filtro de Rango de Fecha ('closed_at')
        $from = $request->query('from');
        $to = $request->query('to');

        if ($from) {
            $historyQuery->whereDate('report_date', '>=', $from);
        }
        if ($to) {
            $historyQuery->whereDate('report_date', '<=', $to);
        }

        // 3. Aplicar filtro de Búsqueda General ('query')
        if ($request->filled('query')) {
            $search = $request->input('query');

            // Usar un grupo de WHERE OR para buscar en múltiples columnas
            $historyQuery->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('internal_code', 'like', "%{$search}%")
                    ->orWhere('equipment_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('fault_status_name', 'like', "%{$search}%")
                    ->orWhere('spare_part_status_name', 'like', "%{$search}%")
                    ->orWhere('service_area_name', 'like', "%{$search}%");
            });
        }

        // 4. Ejecutar y paginar la consulta
        $history = $historyQuery
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString(); // Mantiene los filtros en la paginación

        // 5. Devolver la vista
        return view('V1.AdminBranch.FaultHistory.index', compact("history"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $history = FaultHistory::find($id);
        return view('V1.AdminBranch.FaultHistory.show', compact("history"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
