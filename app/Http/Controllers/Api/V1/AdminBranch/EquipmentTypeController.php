<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\AdminBranch\EquipmentTypeRequest;
use App\Models\EquipmentType;

class EquipmentTypeController extends SimpleCrudApiController
{
    protected string $modelClass = EquipmentType::class;
    protected string $permissionBase = 'Tipos de equipo';
    protected string $resourceName = 'Tipo de equipo';
    protected array $fillableFields = ['name'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name'];
    protected string $defaultSortColumn = 'name';
    protected string $defaultSortDirection = 'asc';

    public function store(EquipmentTypeRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(EquipmentTypeRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
