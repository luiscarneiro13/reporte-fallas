<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ServiceAreaEditRequest;
use App\Http\Requests\V1\ServiceAreaRequest;
use App\Models\ServiceArea;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ServiceAreaController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.service.areas.index";

    public function __construct()
    {
        $basePermission = "Areas de Servicio";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $query = request('query');
        $serviceAreas = ServiceArea::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('V1.AdminBranch.ServiceAreas.index', compact('serviceAreas'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('V1.AdminBranch.ServiceAreas.create', compact('back_url'));
    }

    public function store(ServiceAreaRequest $request)
    {
        try {
            $item = new ServiceArea();
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->branch_id = session('branch')->id;
            $item->save();

            // Elimina la instancia 'fault_data' del contenedor. Esto es para que se recarguen los selects globales
            // Se creará nuevamente en la próxima solicitud y estará disponible en toda la app
            App::forgetInstance('fault_data');

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Area de servicio creada: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $serviceArea = ServiceArea::find($id);
        return view('V1.AdminBranch.ServiceAreas.edit', compact('serviceArea', 'back_url'));
    }

    public function update(ServiceAreaEditRequest $request, string $id)
    {
        try {
            $item = ServiceArea::find($id);
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->save();

            // Elimina la instancia 'fault_data' del contenedor. Esto es para que se recarguen los selects globales
            // Se creará nuevamente en la próxima solicitud y estará disponible en toda la app
            App::forgetInstance('fault_data');

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Area de servicio actualizada: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = ServiceArea::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Area de servicio eliminada: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
