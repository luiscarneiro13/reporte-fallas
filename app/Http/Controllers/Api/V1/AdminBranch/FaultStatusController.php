<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\AdminBranch\FaultStatusRequest;
use App\Models\FaultStatus;
use Illuminate\Http\Request;

/**
 * Spec §6.2.9: catálogo casi siempre completo (pocos estados por sucursal),
 * por eso el índice no pagina — se mantiene esa excepción, mencionada
 * explícitamente como "documentar explícitamente que este catálogo es
 * siempre completo" en vez de forzar paginación aquí.
 */
class FaultStatusController extends SimpleCrudApiController
{
    protected string $modelClass = FaultStatus::class;
    protected string $permissionBase = 'Estatus de fallas';
    protected string $resourceName = 'Estado de falla';
    protected array $fillableFields = ['name'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name'];
    protected string $defaultSortColumn = 'name';
    protected string $defaultSortDirection = 'asc';
    protected bool $paginated = false;

    public function index(Request $request)
    {
        $query = FaultStatus::query()
            ->where('branch_id', BranchHelper::getBranchId())
            ->where('name', '!=', 'closed');

        if ($search = $request->query('query')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $this->applySort($query, $request, $this->sortableColumns, $this->defaultSortColumn, $this->defaultSortDirection);

        return $this->success($query->get());
    }

    public function store(FaultStatusRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(FaultStatusRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
