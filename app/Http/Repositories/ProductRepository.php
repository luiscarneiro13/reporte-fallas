<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{

    public function getAllBranch($branch_id, $query = null)
    {
        return Product::query()
            ->with('modelVehicles')
            ->join('type_articles', 'products.type_article_id', 'type_articles.id')
            ->join('brands', 'products.brand_id', 'brands.id')
            ->select(
                'products.id',
                'products.name',
                'type_articles.name as description',
                'products.price',
                'products.available_qty',
                'brands.name as brand',
                'type_articles.name as type',
            )
            ->where('products.branch_id', $branch_id)
            ->where('products.available_qty', '>', 0)
            ->where(function ($queryBuilder) use ($query) {
                if ((string)$query) {
                    $queryBuilder->where('products.name', 'LIKE', '%' . (string)$query . '%')
                        ->orWhere('products.barcode', 'LIKE', '%' . (string)$query . '%')
                        ->orWhere('type_articles.name', 'LIKE', '%' . (string)$query . '%')
                        ->orWhere('brands.name', 'LIKE', '%' . (string)$query . '%');
                }
            })
            ->take(30)
            ->get();
    }
}
