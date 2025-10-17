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
        // 1. Capturar el ID de la sucursal de la sesión y los parámetros de búsqueda
        $branchId = session('branch')->id;
        $searchName = $request->query('name');                 // Filtro por nombre de empleado
        $query = $request->query('query');                     // Filtro genérico (ID, descripción, ID Interno)
        $searchEquipmentName = $request->query('equipment_name'); // Filtro por nombre de equipo
        $searchServiceAreaName = $request->query('service_area_name'); // Filtro por nombre de área de servicio

        // NUEVOS FILTROS POR ID de estado/ejecutor
        $searchFaultStatusId = $request->query('status_id'); // Filtro por ID de estado de falla
        $searchSparePartStatusId = $request->query('spare_status_id'); // Filtro por ID de estado de repuesto
        $searchExecutorId = $request->query('executor_id'); // Filtro por ID de ejecutor


        // 2. Iniciar la consulta con Carga Ansiosa (Eager Loading)
        $faultsQuery = Fault::with(['reportedBy', 'equipment', 'serviceArea', 'faultStatus', 'sparePartStatus', 'executor'])
            // FILTRAR POR LA SUCURSAL DEL USUARIO EN SESIÓN (Requerimiento)
            ->where('branch_id', $branchId);

        // 3. Aplicar filtro de búsqueda por Nombre de Empleado (Funcionalidad preservada)
        if ($searchName) {
            $faultsQuery->whereHas('reportedBy', function ($q) use ($searchName) {
                $searchTerm = '%' . $searchName . '%';

                // Buscar coincidencia en first_name o last_name del empleado
                $q->where('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm);
            });
        }

        // 4. Aplicar filtro de búsqueda por Nombre de Equipo (NUEVA FUNCIONALIDAD)
        if ($searchEquipmentName) {
            $faultsQuery->whereHas('equipment', function ($q) use ($searchEquipmentName) {
                $q->where('name', 'like', "%{$searchEquipmentName}%");
            });
        }

        // 5. Aplicar filtro de búsqueda por Nombre de Área de Servicio (NUEVA FUNCIONALIDAD)
        if ($searchServiceAreaName) {
            $faultsQuery->whereHas('serviceArea', function ($q) use ($searchServiceAreaName) {
                $q->where('name', 'like', "%{$searchServiceAreaName}%");
            });
        }

        // 6. Aplicar filtro por Estado de Falla (fault_status_id)
        if ($searchFaultStatusId) {
            $faultsQuery->where('fault_status_id', $searchFaultStatusId);
        }

        // 7. Aplicar filtro por Estado de Repuesto (spare_part_status_id)
        if ($searchSparePartStatusId) {
            $faultsQuery->where('spare_part_status_id', $searchSparePartStatusId);
        }

        // 8. Aplicar filtro por Ejecutor (executor_id)
        if ($searchExecutorId) {
            $faultsQuery->where('executor_id', $searchExecutorId);
        }

        // 9. Aplicar filtro de búsqueda genérico (ID/Descripción) (Funcionalidad preservada)
        if ($query) {
            $faultsQuery->where(function ($q) use ($query) {
                // Criterios directos (ID - quitando ceros a la izquierda)
                $q->where('id', ltrim($query, '0'))
                    // Criterio por descripción
                    ->orWhere('description', 'like', "%{$query}%")
                    // Criterio por ID interno
                    ->orWhere('internal_id', 'like', "%{$query}%");
            });
        }

        // 10. Aplicar ordenamiento (Requerimiento: descendente por ID)
        $faultsQuery->orderBy('faults.id', 'desc');

        // 11. Ejecutar la consulta con paginación (Requerimiento: 10 elementos)
        $faults = $faultsQuery->paginate(10);

        // 12. Devolver la vista con los resultados
        // Utilizamos la cadena literal de la vista según tu indicación, pasando todos los filtros.
        return view('V1.AdminBranch.Faults.index', compact(
            'faults',
            'searchName',
            'query',
            'searchEquipmentName',
            'searchServiceAreaName',
            'searchFaultStatusId',
            'searchSparePartStatusId',
            'searchExecutorId'
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
