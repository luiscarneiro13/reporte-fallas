<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Concerns\SimpleCrudApiController;
use App\Http\Requests\Api\V1\Catalog\TypeArticleRequest;
use App\Models\TypeArticle;

class TypeArticleController extends SimpleCrudApiController
{
    protected string $modelClass = TypeArticle::class;
    protected string $permissionBase = 'Tipos de Articulos';
    protected string $resourceName = 'Tipo de artículo';
    protected array $fillableFields = ['name'];
    protected array $searchableColumns = ['name'];
    protected array $sortableColumns = ['id', 'name'];
    protected string $defaultSortColumn = 'name';
    protected string $defaultSortDirection = 'asc';

    public function store(TypeArticleRequest $request)
    {
        return $this->storeItem($request);
    }

    public function update(TypeArticleRequest $request, string $id)
    {
        return $this->updateItem($request, $id);
    }
}
