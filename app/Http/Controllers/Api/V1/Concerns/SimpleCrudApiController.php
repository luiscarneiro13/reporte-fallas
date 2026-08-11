<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Controller;
use App\Traits\Api\ApiResponse;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Base reutilizable para catálogos CRUD simples (branch-scoped o globales),
 * de un puñado de campos, sin lógica de negocio propia. La spec (§6.2 y
 * §6.3) describe muchos de estos recursos como "CRUD estándar igual
 * patrón" — en vez de duplicar index/store/update/destroy en cada
 * controlador, las subclases solo declaran su configuración.
 *
 * Las subclases deben tipar store()/update() con su propio FormRequest
 * (para que Laravel resuelva/valide automáticamente) y delegar en
 * parent::store()/parent::update().
 */
abstract class SimpleCrudApiController extends Controller
{
    use ApiResponse;
    use Sortable;

    protected string $modelClass;
    protected string $permissionBase;
    protected string $resourceName;
    protected array $fillableFields = [];
    protected array $searchableColumns = [];
    protected array $sortableColumns = ['id'];
    protected string $defaultSortColumn = 'id';
    protected string $defaultSortDirection = 'desc';
    protected bool $branchScoped = true;
    protected bool $paginated = true;

    public function __construct()
    {
        $base = $this->permissionBase;
        $this->middleware("permission:{$base} Crear")->only(['store']);
        $this->middleware("permission:{$base} Editar")->only(['update']);
        $this->middleware("permission:{$base} Eliminar")->only(['destroy']);
        $this->middleware("permission:{$base} Ver")->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::query();

        if ($this->branchScoped) {
            $query->where('branch_id', BranchHelper::getBranchId());
        }

        if ($search = $request->query('query')) {
            $query->where(function ($q) use ($search) {
                foreach ($this->searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        $this->applySort($query, $request, $this->sortableColumns, $this->defaultSortColumn, $this->defaultSortDirection);

        if (!$this->paginated) {
            return $this->success($query->get());
        }

        return $this->paginatedResponse($query->paginate(10));
    }

    public function show(string $id)
    {
        $item = $this->findScoped($id);

        if (!$item) {
            return $this->error("{$this->resourceName} no encontrado.", 404);
        }

        return $this->success($item);
    }

    protected function storeItem(Request $request)
    {
        $modelClass = $this->modelClass;
        $item = new $modelClass();

        foreach ($this->fillableFields as $field) {
            $item->{$field} = $request->input($field);
        }

        if ($this->branchScoped) {
            $item->branch_id = BranchHelper::getBranchId();
        }

        $item->save();

        App::forgetInstance('fault_data');

        return $this->created($item, "{$this->resourceName} creado exitosamente.");
    }

    protected function updateItem(Request $request, string $id)
    {
        $item = $this->findScoped($id);

        if (!$item) {
            return $this->error("{$this->resourceName} no encontrado.", 404);
        }

        foreach ($this->fillableFields as $field) {
            $item->{$field} = $request->input($field);
        }

        $item->save();

        App::forgetInstance('fault_data');

        return $this->success($item, "{$this->resourceName} actualizado exitosamente.");
    }

    public function destroy(string $id)
    {
        $item = $this->findScoped($id);

        if (!$item) {
            return $this->error("{$this->resourceName} no encontrado.", 404);
        }

        $item->delete();

        App::forgetInstance('fault_data');

        return $this->success(null, "{$this->resourceName} eliminado exitosamente.");
    }

    protected function findScoped(string $id)
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::query()->where('id', $id);

        if ($this->branchScoped) {
            $query->where('branch_id', BranchHelper::getBranchId());
        }

        return $query->first();
    }
}
