<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Helpers\BranchHelper;
use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\BranchUpdateRequest;
use App\Models\Branch;
use App\Traits\Api\ApiResponse;

class MyBranchController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('permission:Empresa Editar')->only(['update']);
    }

    public function show()
    {
        $branch = Branch::find(BranchHelper::getBranchId());

        if (!$branch) {
            return $this->error('Sucursal no encontrada.', 404);
        }

        return $this->success($branch);
    }

    public function update(BranchUpdateRequest $request, string $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return $this->error('Sucursal no encontrada.', 404);
        }

        $branch->name = $request->input('name');
        $branch->description = $request->input('description');
        $branch->phone = $request->input('phone');
        $branch->email = $request->input('email');
        $branch->rif = $request->input('rif');
        $branch->address = $request->input('address');

        if ($request->hasFile('logo')) {
            $branch->logo = (new Images())->uploadImage($request->file('logo'), 'logos');
        }

        $branch->save();

        return $this->success($branch, "Se actualizó la sucursal {$branch->name}");
    }
}
