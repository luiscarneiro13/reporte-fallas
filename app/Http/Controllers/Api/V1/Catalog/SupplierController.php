<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\Catalog\SupplierRequest;
use App\Models\Supplier;

class SupplierController extends SimpleCrudApiController
{
    protected string $modelClass = Supplier::class;
    protected string $permissionBase = 'Proveedores';
    protected string $resourceName = 'Proveedor';
    protected array $fillableFields = ['name', 'address', 'phone', 'email'];
    protected array $searchableColumns = ['name', 'address', 'phone', 'email'];
    protected array $sortableColumns = ['id', 'name'];
    protected string $defaultSortColumn = 'id';
    protected string $defaultSortDirection = 'desc';

    public function store(SupplierRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(SupplierRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
