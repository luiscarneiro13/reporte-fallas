<?php

namespace App\Http\Repositories\Interfaces;

interface ProductRepositoryInterface
{
    public function getAllBranch($branch_id, $query = null);
}
