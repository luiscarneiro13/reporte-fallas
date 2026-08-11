<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\Catalog\ServiceRequest;
use App\Models\Service;

class ServiceController extends SimpleCrudApiController
{
    protected string $modelClass = Service::class;
    protected string $permissionBase = 'Servicios';
    protected string $resourceName = 'Servicio';
    protected array $fillableFields = ['name', 'price'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name', 'price'];
    protected string $defaultSortColumn = 'id';
    protected string $defaultSortDirection = 'desc';

    public function store(ServiceRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(ServiceRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
