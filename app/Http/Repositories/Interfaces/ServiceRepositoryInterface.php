<?php

namespace App\Http\Repositories\Interfaces;

interface ServiceRepositoryInterface
{
    public function getAllBranch($branch_id, $query = null);
}
