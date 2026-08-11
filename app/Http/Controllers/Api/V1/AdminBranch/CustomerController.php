<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\AdminBranch\CustomerRequest;
use App\Models\Customer;

class CustomerController extends SimpleCrudApiController
{
    protected string $modelClass = Customer::class;
    protected string $permissionBase = 'Clientes';
    protected string $resourceName = 'Cliente';
    protected array $fillableFields = ['name', 'rif', 'address', 'phone', 'email'];
    protected array $searchableColumns = ['name', 'address', 'email', 'phone', 'rif'];
    protected array $sortableColumns = ['id', 'name', 'rif', 'email', 'phone', 'address'];
    protected string $defaultSortColumn = 'id';
    protected string $defaultSortDirection = 'desc';

    public function store(CustomerRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(CustomerRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
