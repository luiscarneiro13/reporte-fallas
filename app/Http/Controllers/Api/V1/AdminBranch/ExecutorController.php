<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminBranch\ExecutorRequest;
use App\Models\Employee;
use App\Traits\Api\ApiResponse;
use App\Traits\Sortable;
use Illuminate\Http\Request;

/**
 * Spec §6.2.6: opera sobre el mismo modelo Employee filtrando executor=1.
 * `executor` siempre se fuerza a 1 en el servidor, ignorando el body.
 */
class ExecutorController extends Controller
{
    use ApiResponse;
    use Sortable;

    const SORTABLE_COLUMNS = ['id', 'identification_number', 'first_name', 'last_name', 'phone_number', 'address', 'external'];

    public function __construct()
    {
        $base = 'Ejecutores';
        $this->middleware("permission:{$base} Crear")->only(['store']);
        $this->middleware("permission:{$base} Editar")->only(['update']);
        $this->middleware("permission:{$base} Eliminar")->only(['destroy']);
        $this->middleware("permission:{$base} Ver")->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Employee::query()
            ->with('serviceAreas:service_areas.id,service_areas.name')
            ->where('branch_id', BranchHelper::getBranchId())
            ->where('executor', 1);

        if ($search = $request->query('query')) {
            $query->where(function ($q) use ($search) {
                $q->where('identification_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $this->applySort($query, $request, self::SORTABLE_COLUMNS, 'first_name', 'asc');

        return $this->paginatedResponse($query->paginate(10));
    }

    public function store(ExecutorRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(ExecutorRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    protected function saveOrUpdate(ExecutorRequest $request, ?string $id = null)
    {
        $branchId = BranchHelper::getBranchId();
        $item = $id
            ? Employee::where('id', $id)->where('branch_id', $branchId)->first()
            : new Employee();

        if ($id && !$item) {
            return $this->error('Ejecutor no encontrado.', 404);
        }

        $item->identification_number = $request->input('identification_number');
        $item->first_name = $request->input('first_name');
        $item->last_name = $request->input('last_name');
        $item->phone_number = $request->input('phone_number');
        $item->address = $request->input('address');
        $item->external = (int) $request->input('external', 0);
        $item->executor = 1;
        $item->branch_id = $branchId;
        $item->save();

        return $this->success($item, $id ? 'Ejecutor actualizado exitosamente.' : 'Ejecutor creado exitosamente.', $id ? 200 : 201);
    }

    public function destroy(string $id)
    {
        $item = Employee::where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$item) {
            return $this->error('Ejecutor no encontrado.', 404);
        }

        $item->delete();

        return $this->success(null, 'Ejecutor eliminado exitosamente.');
    }
}
