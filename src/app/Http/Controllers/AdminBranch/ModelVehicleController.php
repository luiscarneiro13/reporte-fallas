<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Models\ModelVehicle;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class ModelVehicleController extends Controller
{

    use AlertResponser;

    const INDEX = "admin.sucursal.models.vehicles.index";

    public function index()
    {
        $query = request('query');
        $modelVehicles = ModelVehicle::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('AdminBranch.ModelVehicles.index', compact('modelVehicles'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('AdminBranch.ModelVehicles.create', compact('back_url'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $item = new ModelVehicle();
            $item->name = $request->input('name');
            $item->branch_id = session('branch')->id;
            $item->save();

            if ($request->ajax()) {
                return response()->json(['data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Modelo de vehículo guardado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $modelVehicle = ModelVehicle::find($id);
        return view('AdminBranch.ModelVehicles.edit', compact('modelVehicle', 'back_url'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $item = ModelVehicle::find($id);
            $item->name = $request->input('name');
            $item->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Modelo de vehículo actualizado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = ModelVehicle::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Modelo de vehículo: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
