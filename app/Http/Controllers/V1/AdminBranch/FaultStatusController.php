<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FaultStatusEditRequest;
use App\Http\Requests\V1\FaultStatusRequest;
use App\Models\FaultStatus;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FaultStatusController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.fault.statuses.index";

    public function __construct()
    {
        $basePermission = "Estatus de fallas";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $query = request('query');
        $faulStatuses = FaultStatus::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('V1.AdminBranch.FaultStatuses.index', compact('faulStatuses'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('V1.AdminBranch.FaultStatuses.create', compact('back_url'));
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $faultStatus = FaultStatus::find($id);
        return view('V1.AdminBranch.FaultStatuses.edit', compact('faultStatus', 'back_url'));
    }

    public function store(FaultStatusRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(FaultStatusEditRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(Request $request, string $id = null)
    {
        try {

            $item = $id ? FaultStatus::find($id) : new FaultStatus();

            $item->name = $request->input('name');
            $item->branch_id = session('branch')->id;
            $item->save();

            // Elimina la instancia 'fault_data' del contenedor. Esto es para que se recarguen los selects globales
            // Se creará nuevamente en la próxima solicitud y estará disponible en toda la app
            App::forgetInstance('fault_data');

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            $action_msg = $id ? 'actualizada' : 'creada';
            $message = "Status de falla {$action_msg}: " . $item->name;
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            // Manejo de errores
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage()); // Se recomienda logear el error
            return $this->alertError(self::INDEX, "Error al {$action} el status de la falla: " . $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = FaultStatus::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Estatus de falla eliminada: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
