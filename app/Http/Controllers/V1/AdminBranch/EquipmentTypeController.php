<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EquipmentTypeRequest;
use App\Models\EquipmentType;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{

    use AlertResponser;

    const INDEX = "admin.sucursal.equipment.types.index";

    public function __construct()
    {
        $basePermission = "Tipos de equipo";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $query = request('query');
        $equipmentTypes = EquipmentType::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('V1.AdminBranch.EquipmentTypes.index', compact('equipmentTypes'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('V1.AdminBranch.EquipmentTypes.create', compact('back_url'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EquipmentTypeRequest $request)
    {
        try {
            $item = new EquipmentType();
            $item->name = $request->input('name');
            $item->branch_id = session('branch')->id;
            $item->save();

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Tipo de equipo creado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $equipmentType = EquipmentType::find($id);
        return view('V1.AdminBranch.EquipmentTypes.edit', compact('equipmentType', 'back_url'));
    }

    public function update(EquipmentTypeRequest $request, string $id)
    {
        try {
            $item = EquipmentType::find($id);
            $item->name = $request->input('name');
            $item->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Tipo de equipo actualizado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = EquipmentType::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Tipo de equipo eliminado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
