<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.brands.index";

    public function index()
    {
        $query = request('query');
        $brands = Brand::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('AdminBranch.Brands.index', compact('brands'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('AdminBranch.Brands.create', compact('back_url'));
    }

    public function store(BrandRequest $request)
    {
        try {
            $brand = new Brand();
            $brand->name = $request->input('name');
            $brand->branch_id = session('branch')->id;
            $brand->save();

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $brand]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Marca guardada: ' . $brand->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $brand = Brand::find($id);
        return view('AdminBranch.Brands.edit', compact('brand', 'back_url'));
    }

    public function update(BrandRequest $request, string $id)
    {
        try {
            $brand = Brand::find($id);
            $brand->name = $request->input('name');
            $brand->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Marca actualizada: ' . $brand->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $brand = Brand::find($id);
            $brand->delete();
            return $this->alertSuccess(self::INDEX, 'Marca eliminada: ' . $brand->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
