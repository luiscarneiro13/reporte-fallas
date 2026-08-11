<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\Catalog\ModelVehicleRequest;
use App\Models\ModelVehicle;

class ModelVehicleController extends SimpleCrudApiController
{
    protected string $modelClass = ModelVehicle::class;
    protected string $permissionBase = 'Modelos de Vehiculo';
    protected string $resourceName = 'Modelo de vehículo';
    protected array $fillableFields = ['name'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name'];
    protected string $defaultSortColumn = 'name';
    protected string $defaultSortDirection = 'asc';

    public function store(ModelVehicleRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(ModelVehicleRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
