<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\Catalog\MethodPaymentRequest;
use App\Models\MethodPayment;

class MethodPaymentController extends SimpleCrudApiController
{
    protected string $modelClass = MethodPayment::class;
    protected string $permissionBase = 'Metodos de Pago';
    protected string $resourceName = 'Método de pago';
    protected array $fillableFields = ['name', 'currency'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name'];
    protected string $defaultSortColumn = 'id';
    protected string $defaultSortDirection = 'desc';

    public function store(MethodPaymentRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(MethodPaymentRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
