<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\AdminBranch\DivisionRequest;
use App\Models\Division;

class DivisionController extends SimpleCrudApiController
{
    protected string $modelClass = Division::class;
    protected string $permissionBase = 'Divisiones';
    protected string $resourceName = 'División';
    protected array $fillableFields = ['name', 'description'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name', 'description'];
    protected string $defaultSortColumn = 'name';
    protected string $defaultSortDirection = 'asc';

    public function store(DivisionRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(DivisionRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
