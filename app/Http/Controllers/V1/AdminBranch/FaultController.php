<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FaultRequest;
use App\Mail\ReportarFallaEmail;
use App\Models\Fault;
use App\Models\FaultHistory;
use App\Services\FaultService;
use App\Traits\AlertResponser;
use App\Traits\DateTransformerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\FaultView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FaultController extends Controller
{
    use AlertResponser;
    use DateTransformerTrait;

    const INDEX = "admin.sucursal.faults.index";

    public function __construct()
    {
        $basePermission = "Fallas";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        // --- 1. CAPTURA DE PARÁMETROS (NECESARIO PARA COMPACT Y RESTAURAR ESTADO DE FILTROS) ---
        // Estas variables DEBEN existir en el scope de 'index' para que compact() funcione.
        $searchName = $request->query('name');
        $query = $request->query('query');
        $searchEquipmentName = $request->query('equipment_name');
        $searchServiceAreaName = $request->query('service_area_name');
        $equipmentId = $request->query('equipment_id');
        $serviceAreaId = $request->query('service_area_id');
        $searchFaultStatusId = $request->query('fault_status_id');
        $searchSparePartStatusId = $request->query('spare_part_status_id');
        $projectId = $request->query('project_id');
        $closeStatus = $request->query('close_status');
        $searchExecutorId = $request->query('executor_id');
        $from = $request->query('from');
        $to = $request->query('to');

        // Esto es un service provider que carga data inicial de equipos y demás
        $faultData = app('fault_data');

        $equipment = $faultData['equipment']->prepend('Todos', '0');
        $serviceArea = $faultData['serviceArea']->prepend('Todos', '0');
        $faultStatus = $faultData['faultStatus']->prepend('Todos', '0');
        $sparePartStatuses = $faultData['sparePartStatuses']->prepend('Todos', '0');
        $projects = $faultData['projects']->prepend('Todos', '0');

        // --- 2. Obtener la consulta de fallas ya filtrada ---
        $faultsQuery = $this->getFilteredFaultsQuery($request);

        // --- 3. Ejecutar la consulta con paginación ---
        $faults = $faultsQuery->paginate(10)->appends($request->query());

        // --- 4. Devolver la vista con los resultados y parámetros de filtro ---
        return view('V1.AdminBranch.Faults.index', compact(
            'faults',
            // Parámetros de filtro re-definidos para compact()
            'searchName',
            'query',
            'searchEquipmentName',
            'searchServiceAreaName',
            'searchFaultStatusId',
            'searchSparePartStatusId',
            'projectId',
            'searchExecutorId',
            'equipmentId',
            'serviceAreaId',
            'closeStatus',
            'from',
            'to',
            // Datos estáticos
            "equipment",
            "serviceArea",
            "faultStatus",
            "sparePartStatuses",
            "projects"
        ));
    }

    /**
     * Exporta el listado de fallas sin paginación (IMP)
     */
    public function imp(Request $request)
    {
        // --- 1. Obtener la consulta de fallas ya filtrada ---
        $faultsQuery = $this->getFilteredFaultsQuery($request);

        // --- 2. Ejecutar la consulta sin paginación ---
        $faults = $faultsQuery->get();

        return view('V1.AdminBranch.Faults.imp', compact('faults'));
    }

    private function getFilteredFaultsQuery(Request $request)
    {
        // El branch ID es un filtro base para ambas funciones.
        $branchId = session('branch')->id ?? 1; // Usar un default por si acaso no hay sesión

        // --- 1. Captura de Parámetros de Filtro (Reutilizable) ---
        // NOTA: Esta captura es necesaria dentro del método para construir la consulta.
        $searchName = $request->query('name');
        $query = $request->query('query');
        $searchEquipmentName = $request->query('equipment_name');
        $searchServiceAreaName = $request->query('service_area_name');
        $equipmentId = $request->query('equipment_id');
        $serviceAreaId = $request->query('service_area_id');
        $searchFaultStatusId = $request->query('fault_status_id');
        $searchSparePartStatusId = $request->query('spare_part_status_id');
        $projectId = $request->query('project_id');
        $closeStatus = $request->query('close_status');
        $searchExecutorId = $request->query('executor_id');
        $from = $request->query('from');
        $to = $request->query('to');

        // --- 2. Iniciar la consulta con la Vista y filtros base ---
        $faultsQuery = FaultView::query()
            ->where('branch_id', $branchId);
        // ->whereNull('closed_at'); // Se elimina este filtro base si $closeStatus lo controla

        // ----------------------------------------------------
        // APLICACIÓN DE FILTROS CONDICIONALES
        // ----------------------------------------------------

        // Aplicar filtro por Cierre (close_status)
        if (!empty($closeStatus) && $closeStatus != '0') {
            if ($closeStatus == '1') {
                $faultsQuery->whereNotNull('closed_at');
            } elseif ($closeStatus == '2') {
                $faultsQuery->whereNull('closed_at');
            }
        } else {
            // Si $closeStatus no está definido, mantenemos el filtro original de "No Cerradas"
            $faultsQuery->whereNull('closed_at');
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

        // 8. Aplicar filtro por Estado de Falla (fault_status_id)
        if (!empty($searchFaultStatusId) && $searchFaultStatusId != '0') {
            $faultsQuery->where('fault_status_id', $searchFaultStatusId);
        }

        // 9. Aplicar filtro por Estado de Repuesto (spare_part_status_id)
        if (!empty($searchSparePartStatusId) && $searchSparePartStatusId != '0') {
            $faultsQuery->where('spare_part_status_id', $searchSparePartStatusId);
        }

        // 9.1. Aplicar filtro por proyecto (project_id)
        if (!empty($projectId) && $projectId != '0') {
            $faultsQuery->where('project_id', $projectId);
        }

        // 10. Aplicar filtro por Ejecutor (executor_id)
        if (!empty($searchExecutorId) && $searchExecutorId != '0') {
            $faultsQuery->where('executor_id', $searchExecutorId);
        }

        // 11. Aplicar filtro de búsqueda genérico (ID/Descripción/Internal ID/Internal Code)
        if ($query) {
            $faultsQuery->where(function ($q) use ($query) {
                // ltrim para eliminar ceros a la izquierda si busca por ID (e.g., '00123' -> '123')
                $q->where('id', ltrim($query, '0'))
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('internal_id', 'like', "%{$query}%")
                    ->orWhere('internal_code', 'like', "%{$query}%");
            });
        }

        // 12. Aplicar ordenamiento
        $faultsQuery->orderBy('duration_days', 'desc');

        return $faultsQuery;
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;

        $faultData = app('fault_data');

        $equipment = $faultData['equipment'];
        $serviceArea = $faultData['serviceArea'];
        $faultStatus = $faultData['faultStatus'];
        $sparePartStatuses = $faultData['sparePartStatuses'];

        $employeeReported = FaultService::employeeReported();
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

        $faultData = app('fault_data');

        $equipment = $faultData['equipment'];
        $serviceArea = $faultData['serviceArea'];
        $faultStatus = $faultData['faultStatus'];
        $sparePartStatuses = $faultData['sparePartStatuses'];

        $employeeReported = FaultService::employeeReported();
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

    private function saveOrUpdate(FaultRequest $request, string $id = null)
    {
        // Transacciones comentadas SOLO para fines de prueba.
        // DB::beginTransaction();

        try {
            $validatedData = $request->validated();
            $isClosing = isset($validatedData['closed']);

            // 1. Obtener el modelo de la TABLA BASE ('faults') para la edición.
            $item = $id ? Fault::find($id) : new Fault();
            if (!$item) {
                // DB::rollBack(); // Comentado
                return $this->alertError(self::INDEX, 'Falla no encontrada para actualizar.');
            }

            // 2. Transforma fechas
            $dateFields = ['report_date', 'scheduled_execution', 'completed_execution'];
            $validatedData = $this->transformDateFields($validatedData, $dateFields);

            // 3. Asignación Masiva y Guardar en la tabla 'faults'.
            // Esto asignará el campo 'closed' con el valor de la fecha.
            $item->fill($validatedData);
            $item->branch_id = session('branch')->id;
            $item->save();

            $faultView = FaultView::find($item->id);

            // Se envía el correo
            try {
                // $recipient = 'mantenimiento@servicioscasmar.com'; // Cambia esto por tu dirección para probar
                $recipient = 'carneiroluis2@gmail.com'; // Cambia esto por tu dirección para probar

                // 2. Envía el correo
                Mail::to($recipient)->send(new ReportarFallaEmail($faultView->equipment_name, $faultView->description));
            } catch (\Throwable $th) {
                //throw $th;
            }

            // --- LÓGICA DE CIERRE Y MOVIMIENTO AL HISTÓRICO ---

            if ($isClosing) {

                // ⭐ CLAVE: Recargar el registro actualizado usando el Modelo de la VISTA.
                // Esto es necesario porque $item (Fault) solo tiene columnas de la tabla base.
                // Asumiendo que el modelo de la vista es 'FaultView'.
                $historyRecord = FaultView::find($item->id);

                // Verificación de seguridad
                if (!$historyRecord) {
                    // DB::rollBack(); // Comentado
                    return $this->alertError(self::INDEX, 'Error al obtener datos de la vista para archivar.');
                }

                // Aquí deberías hacer un dd($historyRecord->getAttributes());
                // para ver si los nombres desnormalizados (reported_by_name, equipment_name, etc.)
                // están presentes después del save.
                $historyData = $historyRecord->getAttributes();

                // 4. Mapeo y Limpieza de datos antes de la inserción

                // Mapear el ID original de la falla activa
                $historyData['original_fault_id'] = $item->id;

                // Mapeo de la columna de cierre: 'closed' de la tabla/vista a 'closed_at' en el historial.
                if (isset($historyData['closed']) && !isset($historyData['closed_at'])) {
                    $historyData['closed_at'] = $historyData['closed'];
                }

                // Limpiar claves que NO van en la tabla histórica:
                unset($historyData['id']);
                unset($historyData['closed']);
                unset($historyData['duration_days']);

                // 5. Crear el registro en la tabla histórica
                FaultHistory::create($historyData);

                // 6. Eliminar el registro de la tabla de fallas activas
                $item->delete();

                // DB::commit(); // Comentado

                $message = "Falla cerrada y archivada correctamente.";
                return $this->alertSuccess(self::INDEX, $message);
            } else {
                // LÓGICA DE EDICIÓN/CREACIÓN NORMAL
                // DB::commit(); // Comentado

                $action_msg = $id ? 'actualizada' : 'creada';
                $message = "Falla {$action_msg}";

                // Redirección normal
                if ($request->ajax()) {
                    return response()->json(['success' => true, 'data' => $item]);
                }

                if (request()->back_url) {
                    return redirect(request()->back_url);
                }

                return $this->alertSuccess(self::INDEX, $message);
            }
        } catch (\Throwable $th) {
            // DB::rollBack(); // Comentado

            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage());
            return $this->alertError(self::INDEX, "Error al {$action} la falla: " . $th->getMessage());
        }
    }

    public function destroy(string $id) {}
}
