<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TypeArticleApiRequest;
use App\Http\Requests\TypeArticleRequest;
use App\Models\TypeArticle;
use App\Traits\AlertResponser;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeArticleController extends Controller
{
    use ApiResponser;

    public function store(TypeArticleApiRequest $request)
    {
        try {
            $typeArticle = new TypeArticle();
            $typeArticle->name = $request->input('name');
            $typeArticle->branch_id = $request->input('branch_id');
            $typeArticle->save();
            return $this->success($typeArticle);
        } catch (\Throwable $th) {
            return $this->error($th);
        }
    }
}
