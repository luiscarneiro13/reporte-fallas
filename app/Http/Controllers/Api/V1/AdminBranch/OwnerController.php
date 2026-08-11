<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\AdminBranch\OwnerRequest;
use App\Models\Owner;

/**
 * Catálogo global (sin branch_id, ver spec §6.2.10). El original no exigía
 * permiso alguno; aquí sí se exige "Propietarios *" por consistencia con
 * el resto del sistema (recomendación explícita de la spec).
 */
class OwnerController extends SimpleCrudApiController
{
    protected string $modelClass = Owner::class;
    protected string $permissionBase = 'Propietarios';
    protected string $resourceName = 'Propietario';
    protected array $fillableFields = ['first_name', 'last_name'];
    protected array $searchableColumns = ['first_name', 'last_name'];
    protected array $sortableColumns = ['id', 'first_name', 'last_name'];
    protected string $defaultSortColumn = 'last_name';
    protected string $defaultSortDirection = 'asc';
    protected bool $branchScoped = false;

    public function store(OwnerRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(OwnerRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
