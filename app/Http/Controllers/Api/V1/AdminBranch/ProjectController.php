<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminBranch\ProjectRequest;
use App\Models\Project;
use App\Traits\Api\ApiResponse;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Spec §6.2.11: el índice devuelve filas planas (JOIN a customers/divisions),
 * no el modelo Project completo; store/update sí devuelven el modelo completo.
 */
class ProjectController extends Controller
{
    use ApiResponse;
    use Sortable;

    const SORTABLE_COLUMNS = [
        'projects.id',
        'customer_name',
        'project_name',
        'division_name',
        'project_geographic_area',
        'project_contract_number',
    ];

    public function __construct()
    {
        $base = 'Proyectos';
        $this->middleware("permission:{$base} Crear")->only(['store']);
        $this->middleware("permission:{$base} Editar")->only(['update']);
        $this->middleware("permission:{$base} Eliminar")->only(['destroy']);
        $this->middleware("permission:{$base} Ver")->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $branchId = BranchHelper::getBranchId();

        $query = Project::query()
            ->join('customers', 'customers.id', '=', 'projects.customer_id')
            ->join('divisions', 'divisions.id', '=', 'projects.division_id')
            ->where('projects.branch_id', $branchId)
            ->select(
                'projects.id',
                'customers.name as customer_name',
                'projects.name as project_name',
                'divisions.name as division_name',
                'projects.geographic_area as project_geographic_area',
                'projects.contract_number as project_contract_number'
            );

        if ($search = $request->query('query')) {
            $query->where(function ($q) use ($search) {
                $q->where('projects.name', 'like', "%{$search}%")
                    ->orWhere('customers.name', 'like', "%{$search}%")
                    ->orWhere('divisions.name', 'like', "%{$search}%")
                    ->orWhere('projects.geographic_area', 'like', "%{$search}%")
                    ->orWhere('projects.contract_number', 'like', "%{$search}%");
            });
        }

        $this->applySort($query, $request, self::SORTABLE_COLUMNS, 'projects.id', 'desc');

        return $this->paginatedResponse($query->paginate(10));
    }

    public function show(string $id)
    {
        $project = Project::query()->where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$project) {
            return $this->error('Proyecto no encontrado.', 404);
        }

        return $this->success($project);
    }

    public function store(ProjectRequest $request)
    {
        $item = new Project();
        $item->branch_id = BranchHelper::getBranchId();
        $item->customer_id = $request->input('customer_id');
        $item->division_id = $request->input('division_id');
        $item->name = $request->input('name');
        $item->contract_number = $request->input('contract_number');
        $item->description = $request->input('description');
        $item->geographic_area = $request->input('geographic_area');
        $item->save();

        App::forgetInstance('fault_data');

        return $this->created($item, 'Proyecto creado exitosamente.');
    }

    public function update(ProjectRequest $request, string $id)
    {
        $item = Project::query()->where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$item) {
            return $this->error('Proyecto no encontrado.', 404);
        }

        $item->customer_id = $request->input('customer_id');
        $item->division_id = $request->input('division_id');
        $item->name = $request->input('name');
        $item->contract_number = $request->input('contract_number');
        $item->description = $request->input('description');
        $item->geographic_area = $request->input('geographic_area');
        $item->save();

        App::forgetInstance('fault_data');

        return $this->success($item, 'Proyecto actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $item = Project::query()->where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$item) {
            return $this->error('Proyecto no encontrado.', 404);
        }

        $item->delete();

        App::forgetInstance('fault_data');

        return $this->success(null, 'Proyecto eliminado exitosamente.');
    }
}
