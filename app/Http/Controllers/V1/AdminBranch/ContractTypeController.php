<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ContractTypeEditRequest;
use App\Http\Requests\V1\ContractTypeRequest;
use App\Models\ContractType;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ContractTypeController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.contract.types.index";

    public function __construct()
    {
        $basePermission = "Tipos de Contrato";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $query = request('query');
        $contractTypes = ContractType::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('V1.AdminBranch.ContractTypes.index', compact('contractTypes'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('V1.AdminBranch.ContractTypes.create', compact('back_url'));
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $contractType = ContractType::find($id);
        return view('V1.AdminBranch.ContractTypes.edit', compact('contractType', 'back_url'));
    }

    public function store(ContractTypeRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(ContractTypeEditRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(Request $request, string $id = null)
    {
        try {

            $item = $id ? ContractType::find($id) : new ContractType();

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

            $action_msg = $id ? 'actualizado' : 'creado';
            $message = "Tipo de contrato {$action_msg}: " . $item->name;
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            // Manejo de errores
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage()); // Se recomienda logear el error
            return $this->alertError(self::INDEX, "Error al {$action} el tipo de contrato: " . $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = ContractType::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Tipo de contrato eliminado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
