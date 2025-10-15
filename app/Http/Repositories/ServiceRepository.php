<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{

    public function getAllBranch($branch_id, $query = null)
    {
        return Service::query()
            ->select(
                'services.*',
            )
            ->where('services.branch_id', $branch_id)
            ->where(function ($queryBuilder) use ($query) {
                if ((string)$query) {
                    $queryBuilder->where('services.name', 'LIKE', '%' . (string)$query . '%')
                        ->orWhere('brands.name', 'LIKE', '%' . (string)$query . '%');
                }
            })
            ->take(30)
            ->get();
    }
}
