<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\AdminBranch\SparePartStatusRequest;
use App\Models\SparePartStatus;

class SparePartStatusController extends SimpleCrudApiController
{
    protected string $modelClass = SparePartStatus::class;
    protected string $permissionBase = 'Estatus de repuestos';
    protected string $resourceName = 'Estado de repuesto';
    protected array $fillableFields = ['name'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name'];
    protected string $defaultSortColumn = 'name';
    protected string $defaultSortDirection = 'asc';

    public function store(SparePartStatusRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(SparePartStatusRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
