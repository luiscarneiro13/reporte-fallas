<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EquipmentEditRequest;
use App\Http\Requests\V1\EquipmentRequest;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\FaultHistory;
use App\Models\Project;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EquipmentController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.equipment.index";

    public function __construct()
    {
        $basePermission = "Equipos";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        // 1. Capturar el ID de la sucursal de la sesión
        $branchId = session('branch')->id;

        // 2. Capturar el parámetro de búsqueda
        $query = $request->query('query');

        // 3. Iniciar la consulta con Carga Ansiosa (Eager Loading)
        $equipmentQuery = Equipment::query()
            // FILTRAR POR LA SUCURSAL DEL USUARIO EN SESIÓN
            ->where('branch_id', $branchId)
            ->with(
                [
                    // Último Proyecto (solo id y name)
                    'lastProject' => function ($projectQuery) {
                        $projectQuery->select('projects.id', 'name');
                    },
                ],
                'history'
            );

        // 4. Aplicar filtro de búsqueda si existe un query
        if ($query) {
            $equipmentQuery->where(function ($q) use ($query) {

                // Criterios directos (placa, código interno)
                $q->where('id', ltrim($query, '0'))
                    ->orWhere('placa', 'like', "%{$query}%")
                    ->orWhere('model_year', 'like', "%{$query}%")
                    ->orWhere('internal_code', 'like', "%{$query}%")
                    ->orWhere('color', 'like', "%{$query}%")
                ;

                // Criterio de relación (nombre del proyecto)
                // Se usa 'projects' para buscar en la relación Muchos a Muchos completa
                $q->orWhereHas('projects', function ($projectQuery) use ($query) {
                    $projectQuery->where('name', 'like', "%{$query}%");
                });
            });
        }

        $equipmentQuery->orderBy('equipment.id', 'desc');
        // 5. Ejecutar la consulta con paginación
        $equipment = $equipmentQuery->paginate(10);

        // 6. Devolver la vista con los resultados
        return view('V1.AdminBranch.Equipment.index', compact('equipment'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        $projectsCollection = Project::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $projects = $projectsCollection->prepend('Stand by / Sin Proyecto', '0');
        $modelYears = $this->getModelYears();
        $equipmentTypesCollection = EquipmentType::where('branch_id', session('branch')->id)->pluck('name', 'name');
        $equipmentTypes = $equipmentTypesCollection->prepend('Seleccione', '0');
        return view('V1.AdminBranch.Equipment.create', compact('back_url', 'projects', 'modelYears', 'equipmentTypes'));
    }

    public function show(string $id)
    {
        $back_url = request()->back_url ?? null;
        $equipment = Equipment::query()
            ->where('id', $id)->first();

        $history = FaultHistory::where('equipment_id', $id)->get();

        // return view('V1.AdminBranch.Equipment.show', compact('back_url', 'equipment', 'history'));
        return view('V1.AdminBranch.Equipment.show', compact('back_url', 'equipment', 'history'));
    }

    public function imp(string $id)
    {
        $back_url = request()->back_url ?? null;
        $equipment = Equipment::query()->where('id', $id)->first();

        $history = FaultHistory::where('equipment_id', $id)->get();

        // return view('V1.AdminBranch.Equipment.show', compact('back_url', 'equipment', 'history'));
        return view('V1.AdminBranch.Equipment.imp', compact('back_url', 'equipment', 'history'));
    }

    /**
     * Exporta el listado de todos los equipos
     */
    public function impAll(Request $request)
    {
        $back_url = request()->back_url ?? null;
        $equipment = Equipment::all();

        return view('V1.AdminBranch.Equipment.impAll', compact('equipment'));
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $projectsCollection = Project::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $projects = $projectsCollection->prepend('Stand by / Sin Proyecto', '0');
        $equipment = Equipment::query()
            ->where('id', $id)
            ->with([
                'lastProject' => function ($projectQuery) {
                    $projectQuery->select('projects.id', 'name');
                },
            ])->first();
        $modelYears = $this->getModelYears();
        $equipmentTypesCollection = EquipmentType::where('branch_id', session('branch')->id)->pluck('name', 'name');
        $equipmentTypes = $equipmentTypesCollection->prepend('Seleccione', '0');

        return view('V1.AdminBranch.Equipment.edit', compact('back_url', 'projects', 'equipment', 'modelYears', 'equipmentTypes'));
    }

    public function store(EquipmentRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(EquipmentEditRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(Request $request, string $id = null)
    {
        try {
            // 1. Prepara el array de Project IDs.
            $projectIds = $request->input('project_id');

            // Si el input no es un array, se convierte a uno. Si es nulo, se convierte a [].
            if (!is_array($projectIds)) {
                $projectIds = $projectIds ? [$projectIds] : [];
            }

            // 2. Buscar o crear el equipo
            $item = $id ? Equipment::find($id) : new Equipment();

            if (!$item) {
                return $this->alertError(self::INDEX, 'Equipo no encontrado para actualizar.');
            }

            // 3. Asignación de TODOS los atributos al modelo Equipment

            // Atributos Identificadores y Seriales Técnicos
            $item->placa = $request->input('placa');
            $item->serial_niv = $request->input('serial_niv');
            $item->body_serial_number = $request->input('body_serial_number');
            $item->chassis_serial_number = $request->input('chassis_serial_number');
            // $item->engine_serial_number = $request->input('engine_serial_number');
            $item->racda = $request->input('racda'); // Campo específico
            $item->owner = $request->input('owner'); // (Asumo que lo necesitas)
            $item->internal_code = $request->input('internal_code'); // (Asumo que lo necesitas)

            // Atributos de Características del Vehículo
            $item->vehicle_model = $request->input('vehicle_model');
            $item->brand_name = $request->input('brand_name');
            $item->model_year = $request->input('model_year');
            $item->color = $request->input('color');
            $item->origin = $request->input('origin');
            $item->type = $request->input('type');

            // Atributo de sucursal
            $item->branch_id = session('branch')->id; // Asignación de la sucursal actual

            // 4. Guardar el modelo Equipment
            // El equipo debe guardarse primero para obtener su ID ($item->id) antes de la sincronización.
            $item->save();

            // 5. Sincronizar la relación Muchos a Muchos (Tabla Pivote) 🎯
            // Esto maneja la asignación, actualización y desasignación (borrado) de proyectos.
            $item->projects()->sync($projectIds);

            // Elimina la instancia 'fault_data' del contenedor. Esto es para que se recarguen los selects globales
            // Se creará nuevamente en la próxima solicitud y estará disponible en toda la app
            App::forgetInstance('fault_data');


            // 6. Retornos y mensajes
            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            $action_msg = $id ? 'actualizado' : 'creado';
            $message = "Equipo {$action_msg} con placa: " . $item->placa;
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            // Manejo de errores
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage()); // Se recomienda logear el error
            return $this->alertError(self::INDEX, "Error al {$action} el equipo: " . $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = Equipment::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Equipo eliminado: ' . $item->placa);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    function getModelYears(int $startYear = 1940): array
    {
        // Obtiene el año actual
        $endYear = date('Y');

        // 1. Genera el rango de números y lo convierte en una Colección de Laravel.
        $yearsCollection = collect(range($startYear, $endYear));

        // 2. Invierte la colección para tener los años más nuevos primero.
        $yearsCollection = $yearsCollection->reverse();

        // 3. Mapea la colección para que el valor (año) se use como clave y valor.
        // Usamos strval para asegurar que tanto la clave como el valor son strings.
        $modelYears = $yearsCollection->mapWithKeys(function ($year) {
            $yearString = strval($year);
            // Retorna un array [clave => valor]
            return [$yearString => $yearString];
        });

        $modelYears->prepend('N/A', '');

        // 4. Retorna el array subyacente de la colección.
        return $modelYears->all();
    }
}
