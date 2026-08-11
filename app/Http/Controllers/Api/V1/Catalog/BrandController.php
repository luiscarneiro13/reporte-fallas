<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\Catalog\BrandRequest;
use App\Models\Brand;

class BrandController extends SimpleCrudApiController
{
    protected string $modelClass = Brand::class;
    protected string $permissionBase = 'Marcas';
    protected string $resourceName = 'Marca';
    protected array $fillableFields = ['name'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name'];
    protected string $defaultSortColumn = 'name';
    protected string $defaultSortDirection = 'asc';

    public function store(BrandRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(BrandRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
