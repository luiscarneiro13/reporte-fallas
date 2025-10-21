<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FaultRequest;
use App\Models\Fault;
use App\Services\FaultService;
use App\Traits\AlertResponser;
use App\Traits\DateTransformerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\FaultView;

class FaultController extends Controller
{
    use AlertResponser;
    use DateTransformerTrait;

    const INDEX = "admin.sucursal.faults.index";

    public function index(Request $request)
    {
        // --- Variables de Selects (Se mantienen sin cambios) ---
        $equipment = FaultService::equipment()->prepend('Todos', '0');
        $serviceArea = FaultService::serviceArea()->prepend('Todos', '0');
        $faultStatus = FaultService::faultStatus()->prepend('Todos', '0');
        $sparePartStatuses = FaultService::sparePartStatuses()->prepend('Todos', '0');

        // --- 1. Captura de Parámetros (¡AQUÍ ESTÁ LA CORRECCIÓN!) ---
        $branchId = session('branch')->id;

        // Filtros de búsqueda (Texto, Query)
        $searchName = $request->query('name'); // Filtro por nombre (reportado por)
        $query = $request->query('query');     // Búsqueda genérica
        $searchEquipmentName = $request->query('equipment_name');
        $searchServiceAreaName = $request->query('service_area_name');

        // Filtros por ID (Usando los nombres exactos de la URL)
        $equipmentId = $request->query('equipment_id');             // ⭐ NUEVO/CORREGIDO
        $serviceAreaId = $request->query('service_area_id');         // ⭐ NUEVO/CORREGIDO
        $searchFaultStatusId = $request->query('fault_status_id');   // ⭐ CORREGIDO (Antes 'status_id')
        $searchSparePartStatusId = $request->query('spare_part_status_id'); // ⭐ CORREGIDO (Antes 'spare_status_id')

        // Otros filtros
        $closeStatus = $request->query('close_status');
        $searchExecutorId = $request->query('executor_id');
        $from = $request->query('from');
        $to = $request->query('to');


        // --- 2. Iniciar la consulta con la Vista ---
        $faultsQuery = FaultView::query()
            ->where('branch_id', $branchId)
            ->whereNull('closed_at')
            ;


        // ----------------------------------------------------
        // APLICACIÓN DE FILTROS DIRECTOS
        // ----------------------------------------------------

        // Aplicar filtro por Cierre (close_status)
        if (!empty($closeStatus) && $closeStatus != '0') {
            if ($closeStatus == '1') {
                $faultsQuery->whereNotNull('closed_at');
            } elseif ($closeStatus == '2') {
                $faultsQuery->whereNull('closed_at');
            }
        }

        // 3. Aplicar filtro de búsqueda por Nombre de Empleado (reported_by_name)
        if ($searchName) {
            $faultsQuery->where('reported_by_name', 'like', "%{$searchName}%");
        }

        // 4. Aplicar filtro por ID de Equipo
        if (!empty($equipmentId) && $equipmentId != '0') {
            $faultsQuery->where('equipment_id', $equipmentId);
        }
        // 4b. Aplicar filtro de búsqueda por Nombre de Equipo
        if ($searchEquipmentName) {
            $faultsQuery->where('equipment_name', 'like', "%{$searchEquipmentName}%");
        }

        // 5. Aplicar filtro por ID de Área de Servicio
        if (!empty($serviceAreaId) && $serviceAreaId != '0') {
            $faultsQuery->where('service_area_id', $serviceAreaId);
        }
        // 5b. Aplicar filtro de búsqueda por Nombre de Área de Servicio
        if ($searchServiceAreaName) {
            $faultsQuery->where('service_area_name', 'like', "%{$searchServiceAreaName}%");
        }

        // 6. Aplicar filtro de Rango de Fechas (report_date)
        if ($from) {
            $faultsQuery->whereDate('report_date', '>=', $from);
        }
        if ($to) {
            $faultsQuery->whereDate('report_date', '<=', $to);
        }

        // 8. Aplicar filtro por Estado de Falla (fault_status_id) - ¡Funciona ahora!
        if (!empty($searchFaultStatusId) && $searchFaultStatusId != '0') {
            $faultsQuery->where('fault_status_id', $searchFaultStatusId);
        }

        // 9. Aplicar filtro por Estado de Repuesto (spare_part_status_id) - ¡Funciona ahora!
        if (!empty($searchSparePartStatusId) && $searchSparePartStatusId != '0') {
            $faultsQuery->where('spare_part_status_id', $searchSparePartStatusId);
        }

        // 10. Aplicar filtro por Ejecutor (executor_id)
        if (!empty($searchExecutorId) && $searchExecutorId != '0') {
            $faultsQuery->where('executor_id', $searchExecutorId);
        }

        // 11. Aplicar filtro de búsqueda genérico (ID/Descripción/Internal ID/Internal Code)
        if ($query) {
            $faultsQuery->where(function ($q) use ($query) {
                $q->where('id', ltrim($query, '0'))
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('internal_id', 'like', "%{$query}%")
                    ->orWhere('internal_code', 'like', "%{$query}%"); // Agregado para internal_code
            });
        }

        // 12. Aplicar ordenamiento
        $faultsQuery->orderBy('id', 'desc');

        // 13. Ejecutar la consulta con paginación

        $faults = $faultsQuery->paginate(10)->appends($request->query());

        // 14. Devolver la vista con los resultados
        return view('V1.AdminBranch.Faults.index', compact(
            'faults',
            'searchName',
            'query',
            'searchEquipmentName',
            'searchServiceAreaName',
            // IDs Corregidos (Para restaurar estado de los selects)
            'searchFaultStatusId',
            'searchSparePartStatusId',
            'searchExecutorId',
            'equipmentId',
            'serviceAreaId',
            'closeStatus',
            // Fechas y Selects estáticos
            'from',
            'to',
            "equipment",
            "serviceArea",
            "faultStatus",
            "sparePartStatuses"
        ));
    }


    public function create()
    {
        $back_url = request()->back_url ?? null;
        $employeeReported = FaultService::employeeReported();
        $equipment = FaultService::equipment();
        $serviceArea = FaultService::serviceArea();
        $faultStatus = FaultService::faultStatus();
        $sparePartStatuses = FaultService::sparePartStatuses();
        $executors = FaultService::executors();
        return view(
            'V1.AdminBranch.Faults.create',
            compact('back_url')
                + compact('employeeReported')
                + compact('equipment')
                + compact('serviceArea')
                + compact('faultStatus')
                + compact('sparePartStatuses')
                + compact('executors')
        );
    }
    public function edit(string $id)
    {

        $fault = Fault::find($id);

        if (!$fault) {
            return $this->alertError(self::INDEX, 'Falla no encontrada.');
        }

        $back_url = request()->back_url ?? null;
        $employeeReported = FaultService::employeeReported();
        $equipment = FaultService::equipment();
        $serviceArea = FaultService::serviceArea();
        $faultStatus = FaultService::faultStatus();
        $sparePartStatuses = FaultService::sparePartStatuses();
        $executors = FaultService::executors();

        return view(
            'V1.AdminBranch.Faults.edit',
            compact('fault', 'back_url')
                + compact('employeeReported')
                + compact('equipment')
                + compact('serviceArea')
                + compact('faultStatus')
                + compact('sparePartStatuses')
                + compact('executors')
        );
    }

    public function store(FaultRequest $request)
    {
        // return $request->validated();
        return $this->saveOrUpdate($request);
    }

    public function update(FaultRequest $request, string $id)
    {
        // El tipo de $request en el método update es FaultRequest
        return $this->saveOrUpdate($request, $id);
    }

    /**
     * Guarda o actualiza una falla usando los datos validados del FaultRequest.
     * La lógica de negocio más compleja se ha movido a métodos privados y al Modelo.
     *
     * @param FaultRequest $request Los datos ya validados.
     * @param string|null $id El ID de la falla a actualizar.
     */
    private function saveOrUpdate(FaultRequest $request, string $id = null)
    {
        try {
            $validatedData = $request->validated();

            // 1. Obtener o crear el modelo
            $item = $id ? Fault::find($id) : new Fault();
            if (!$item) {
                return $this->alertError(self::INDEX, 'Falla no encontrada para actualizar.');
            }

            // 2. Transforma fechas usando el Trait helper.
            $dateFields = ['report_date', 'scheduled_execution', 'completed_execution'];

            // 👈 LLAMADA AL TRAIT: Se invoca como un método de la clase
            $validatedData = $this->transformDateFields($validatedData, $dateFields);

            // 3. Asignación Masiva Segura (Mass Assignment)
            $item->fill($validatedData);
            $item->branch_id = session('branch')->id;

            $item->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            $action_msg = $id ? 'actualizada' : 'creada';
            $message = "Falla {$action_msg} ";
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            // Manejo de errores
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage());
            return $this->alertError(self::INDEX, "Error al {$action} la falla: " . $th->getMessage());
        }
    }

    public function destroy(string $id) {}
}
