<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\AdminBranch\ServiceAreaRequest;
use App\Models\ServiceArea;

class ServiceAreaController extends SimpleCrudApiController
{
    protected string $modelClass = ServiceArea::class;
    protected string $permissionBase = 'Areas de Servicio';
    protected string $resourceName = 'Área de servicio';
    protected array $fillableFields = ['name', 'description'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name', 'description'];
    protected string $defaultSortColumn = 'name';
    protected string $defaultSortDirection = 'asc';

    public function store(ServiceAreaRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(ServiceAreaRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
