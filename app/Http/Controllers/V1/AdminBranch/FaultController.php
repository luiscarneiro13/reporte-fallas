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

class FaultController extends Controller
{
    use AlertResponser;
    use DateTransformerTrait;

    const INDEX = "admin.sucursal.faults.index";

    public function index(Request $request)
    {
        $initValue = collect(["0" => "Todos"]);
        // 1. Equipos
        $equipment = FaultService::equipment()->prepend('Todos', '0');

        // 2. Áreas de Servicio
        $serviceArea = FaultService::serviceArea()->prepend('Todos', '0');

        // 3. Estados de Falla
        $faultStatus = FaultService::faultStatus()->prepend('Todos', '0');

        // 4. Estados de Repuesto
        $sparePartStatuses = FaultService::sparePartStatuses()->prepend('Todos', '0');

        // Ajuste del array $close para incluir 'Todos' (valor '0')
        $close = collect([
            '0' => 'Todos',
            '1' => 'Cerradas',
            '2' => 'Abiertas'
        ]);

        // 1. Capturar el ID de la sucursal de la sesión y los parámetros de búsqueda
        $branchId = session('branch')->id;

        // Filtros de búsqueda existentes (Por nombre o texto)
        $searchName = $request->query('name');
        $query = $request->query('query');
        $searchEquipmentName = $request->query('equipment_name');
        $searchServiceAreaName = $request->query('service_area_name');

        // Filtros por ID existentes (TUS NOMBRES ORIGINALES)
        // NOTA: Estos son los nombres que tu código original usaba para la URL,
        // pero los selects que estamos añadiendo usan 'fault_status_id', etc.
        $searchFaultStatusId = $request->query('status_id');
        $searchSparePartStatusId = $request->query('spare_status_id');
        $searchExecutorId = $request->query('executor_id');

        // ⭐ CAPTURAS DE LOS IDS DESDE LOS SELECTS (NOMBRES DEL BLADE)
        $equipmentId = $request->query('equipment_id');
        $serviceAreaId = $request->query('service_area_id');
        $closeStatus = $request->query('close_status');

        // Filtros de fecha
        $from = $request->query('from'); // Fecha de inicio para la búsqueda (report_date)
        $to = $request->query('to');     // Fecha de fin para la búsqueda (report_date)


        // 2. Iniciar la consulta con Carga Ansiosa (Eager Loading)
        $faultsQuery = Fault::with(['reportedBy', 'equipment', 'serviceArea', 'faultStatus', 'sparePartStatus', 'executor'])
            ->where('branch_id', $branchId);


        // ----------------------------------------------------
        // ⭐ APLICACIÓN DE FILTROS POR ID (equipment_id, service_area_id, close_status)
        // ----------------------------------------------------


        // Aplicar filtro por Cierre (close_status)
        if (!empty($closeStatus) && $closeStatus != '0') {
            if ($closeStatus == '1') { // Cerradas
                $faultsQuery->whereNotNull('closed_at');
            } elseif ($closeStatus == '2') { // Abiertas
                $faultsQuery->whereNull('closed_at');
            }
        }


        // ----------------------------------------------------
        // APLICACIÓN DE FILTROS ORIGINALES (Nombres, IDs viejos y Fechas)
        // ----------------------------------------------------

        // 3. Aplicar filtro de búsqueda por Nombre de Empleado (Funcionalidad preservada)
        if ($searchName) {
            $faultsQuery->whereHas('reportedBy', function ($q) use ($searchName) {
                $searchTerm = '%' . $searchName . '%';
                $q->where('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm);
            });
        }

        // 4. Aplicar filtro de búsqueda por Nombre de Equipo (Funcionalidad preservada)
        if ($searchEquipmentName) {
            $faultsQuery->whereHas('equipment', function ($q) use ($searchEquipmentName) {
                $q->where('name', 'like', "%{$searchEquipmentName}%");
            });
        }

        if ($equipmentId) {
            $faultsQuery->whereHas('equipment', function ($q) use ($equipmentId) {
                $q->where('id', $equipmentId);
            });
        }

        // 5. Aplicar filtro de búsqueda por Nombre de Área de Servicio (Funcionalidad preservada)
        if ($searchServiceAreaName) {
            $faultsQuery->whereHas('serviceArea', function ($q) use ($searchServiceAreaName) {
                $q->where('name', 'like', "%{$searchServiceAreaName}%");
            });
        }

        if ($serviceAreaId) {
            $faultsQuery->whereHas('serviceArea', function ($q) use ($serviceAreaId) {
                $q->where('id', $serviceAreaId);
            });
        }

        // ⭐ 6. Aplicar filtro de Rango de Fechas (report_date)
        if ($from) {
            $faultsQuery->whereDate('report_date', '>=', $from);
        }
        if ($to) {
            $faultsQuery->whereDate('report_date', '<=', $to);
        }

        // 8. Aplicar filtro por Estado de Falla (fault_status_id - Usando el viejo 'status_id')
        if ($searchFaultStatusId) {
            $faultsQuery->where('fault_status_id', $searchFaultStatusId);
        }

        // 9. Aplicar filtro por Estado de Repuesto (spare_part_status_id - Usando el viejo 'spare_status_id')
        if ($searchSparePartStatusId) {
            $faultsQuery->where('spare_part_status_id', $searchSparePartStatusId);
        }

        // 10. Aplicar filtro por Ejecutor (executor_id)
        if ($searchExecutorId) {
            $faultsQuery->where('executor_id', $searchExecutorId);
        }

        // 11. Aplicar filtro de búsqueda genérico (ID/Descripción/Internal ID)
        if ($query) {
            $faultsQuery->where(function ($q) use ($query) {
                $q->where('id', ltrim($query, '0'))
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('internal_id', 'like', "%{$query}%");
            });
        }

        // 12. Aplicar ordenamiento (Requerimiento: descendente por ID)
        $faultsQuery->orderBy('faults.id', 'desc');

        // 13. Ejecutar la consulta con paginación (Requerimiento: 10 elementos)
        $faults = $faultsQuery->paginate(10);
        // return $faults;
        // 14. Devolver la vista con los resultados
        return view('V1.AdminBranch.Faults.index', compact(
            'faults',
            'searchName',
            'query',
            'searchEquipmentName',
            'searchServiceAreaName',
            // IDs Originales (para restaurar estado)
            'searchFaultStatusId',
            'searchSparePartStatusId',
            'searchExecutorId',
            // IDs de Selects NUEVOS (para restaurar estado)
            'equipmentId',       // ⭐ NUEVO (Para el select equipment_id)
            'serviceAreaId',     // ⭐ NUEVO (Para el select service_area_id)
            'closeStatus',       // ⭐ NUEVO (Para el select close_status)
            // Variables de fecha (restauran inputs)
            'from',
            'to',
            // Variables para rellenar los selects
            "equipment",
            "serviceArea",
            "faultStatus",
            "sparePartStatuses",
            "close"
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
