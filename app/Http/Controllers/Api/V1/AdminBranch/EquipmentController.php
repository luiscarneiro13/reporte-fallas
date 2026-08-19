<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminBranch\EquipmentRequest;
use App\Models\Equipment;
use App\Services\EquipmentService;
use App\Traits\Api\ApiResponse;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Spec §6.2.4. Fase 1 no incluye QR ni búsqueda por UUID (`GET /equipos/uuid/{uuid}`):
 * requieren columnas nuevas (uuid, qr_code_path) y el paquete endroid/qr-code,
 * que hoy no existen en el proyecto — ver checklist en docs/api-endpoints-spec.md.
 * Sí se implementa `GET /equipos/{id}/historial` reutilizando la relación
 * `history()` ya existente en el modelo.
 */
class EquipmentController extends Controller
{
    use ApiResponse;
    use Sortable;

    const SORTABLE_COLUMNS = ['id', 'internal_code', 'type', 'placa', 'brand_name', 'vehicle_model', 'model_year', 'color'];

    public function __construct()
    {
        $base = 'Equipos';
        $this->middleware("permission:{$base} Crear")->only(['store']);
        $this->middleware("permission:{$base} Editar")->only(['update']);
        $this->middleware("permission:{$base} Eliminar")->only(['destroy']);
        $this->middleware("permission:{$base} Ver")->only(['index', 'show', 'historial']);
    }

    public function index(Request $request)
    {
        $branchId = BranchHelper::getBranchId();

        $query = Equipment::query()
            ->with('lastProject:projects.id,projects.name')
            ->where('branch_id', $branchId);

        if ($search = $request->query('query')) {
            if (is_numeric($search)) {
                $query->where('id', $search);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('placa', 'like', "%{$search}%")
                        ->orWhere('model_year', 'like', "%{$search}%")
                        ->orWhere('internal_code', 'like', "%{$search}%")
                        ->orWhere('color', 'like', "%{$search}%")
                        ->orWhereHas('projects', function ($projectQuery) use ($search) {
                            $projectQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }
        }

        // Filtros propios (independientes de `query`), igual que la pantalla web.
        if ($internalCode = $request->query('internal_code')) {
            $query->where('internal_code', 'like', "%{$internalCode}%");
        }

        if (($projectId = $request->query('project_id')) && $projectId != '0') {
            $query->whereHas('projects', function ($projectQuery) use ($projectId) {
                $projectQuery->where('projects.id', $projectId);
            });
        }

        $active = $request->query('active');
        if ($active !== null && $active !== '') {
            $query->where('active', (bool) $active);
        }

        $this->applySort($query, $request, self::SORTABLE_COLUMNS, 'id', 'desc');

        return $this->paginatedResponse($query->paginate(10));
    }

    /**
     * Catálogos para el formulario de crear/editar equipo (proyectos, tipos,
     * años, opciones de RACDA), mismos datos y mismo texto que
     * V1\AdminBranch\EquipmentController::create()/edit() en la web.
     */
    public function createData()
    {
        $branchId = BranchHelper::getBranchId();

        if (!$branchId) {
            return $this->error('No hay una sucursal asociada al usuario autenticado.', 400);
        }

        return $this->success([
            'projects' => $this->toOptions(EquipmentService::projectsForForm($branchId)->prepend('Stand by / Sin Proyecto', '0')),
            'equipment_types' => $this->toOptions(EquipmentService::equipmentTypes($branchId)->prepend('Seleccione', '0')),
            'model_years' => $this->toOptions(EquipmentService::modelYears()),
            // OJO: en la web esto es Form::select('racda', ['Si','No','N/A'], ...)
            // sin claves explícitas — Laravel usa el índice numérico (0,1,2) como
            // valor real guardado en equipment.racda, NO el texto. Se replica tal
            // cual para que el dato grabado sea compatible con la web.
            'racda_options' => $this->toOptions(['0' => 'Si', '1' => 'No', '2' => 'N/A']),
        ]);
    }

    public function show(string $id)
    {
        $equipment = Equipment::where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$equipment) {
            return $this->error('Equipo no encontrado.', 404);
        }

        return $this->success($equipment);
    }

    public function historial(string $id)
    {
        $equipment = Equipment::where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$equipment) {
            return $this->error('Equipo no encontrado.', 404);
        }

        return $this->success([
            'back_url' => null,
            'equipment' => $equipment,
            'history' => $equipment->history()->get(),
        ]);
    }

    public function store(EquipmentRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(EquipmentRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    protected function saveOrUpdate(EquipmentRequest $request, ?string $id = null)
    {
        $branchId = BranchHelper::getBranchId();
        $item = $id
            ? Equipment::where('id', $id)->where('branch_id', $branchId)->first()
            : new Equipment();

        if ($id && !$item) {
            return $this->error('Equipo no encontrado.', 404);
        }

        $item->placa = $request->input('placa');
        $item->serial_niv = $request->input('serial_niv');
        $item->body_serial_number = $request->input('body_serial_number');
        $item->chassis_serial_number = $request->input('chassis_serial_number');
        // El original olvidaba asignar engine_serial_number pese a validarlo (spec §6.2.4) — corregido.
        $item->engine_serial_number = $request->input('engine_serial_number');
        $item->vehicle_model = $request->input('vehicle_model');
        $item->type = $request->input('type');
        $item->brand_name = $request->input('brand_name');
        $item->owner = $request->input('owner');
        $item->internal_code = $request->input('internal_code');
        $item->color = $request->input('color');
        $item->origin = $request->input('origin');
        // model_year, racda y active también estaban validados/fillable pero
        // nunca se asignaban acá — se perdían en cada store/update por la API.
        $item->model_year = $request->input('model_year');
        $item->racda = $request->input('racda');
        $item->active = $request->boolean('active', true);
        $item->branch_id = $branchId;
        $item->save();

        $projectIds = $request->input('project_id', []);
        if (!is_array($projectIds)) {
            $projectIds = $projectIds ? [$projectIds] : [];
        }
        $item->projects()->sync($projectIds);

        App::forgetInstance('fault_data');

        return $this->success($item, $id ? 'Equipo actualizado exitosamente.' : 'Equipo creado exitosamente.', $id ? 200 : 201);
    }

    public function destroy(string $id)
    {
        $item = Equipment::where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$item) {
            return $this->error('Equipo no encontrado.', 404);
        }

        $item->delete();

        App::forgetInstance('fault_data');

        return $this->success(null, 'Equipo eliminado exitosamente.');
    }
}
