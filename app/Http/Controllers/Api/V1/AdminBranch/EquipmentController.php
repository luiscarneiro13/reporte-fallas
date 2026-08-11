<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminBranch\EquipmentRequest;
use App\Models\Equipment;
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

        $this->applySort($query, $request, self::SORTABLE_COLUMNS, 'id', 'desc');

        return $this->paginatedResponse($query->paginate(10));
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
