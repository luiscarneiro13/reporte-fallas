<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EmployeeRequest;
use App\Models\Employee;
use App\Models\ServiceArea;
use App\Models\User;
use App\Services\UserService;
use App\Traits\AlertResponser;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class ExecutorController extends Controller
{
    use AlertResponser;
    use Sortable;

    const INDEX = "admin.sucursal.executors.index";

    const SORTABLE_COLUMNS = [
        'external',
        'identification_number',
        'phone_number',
        'address',
    ];

    public function __construct()
    {
        $basePermission = "Proveedores";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = request('query');
        $executorsQuery = Employee::query()
            ->with(['serviceAreas:service_areas.id,service_areas.name'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('identification_number', 'like', "%{$query}%")
                        ->orWhere('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('phone_number', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%")
                        ->orWhereHas('serviceAreas', function ($serviceAreaQuery) use ($query) {
                            $serviceAreaQuery->where('name', 'like', "%{$query}%");
                        })
                    ;
                });
            })
            ->where('branch_id', session('branch')->id)
            ->where('executor', 1)
            ->where('external', 1);

        [$sortBy, $sortDir] = $this->applySort($executorsQuery, $request, self::SORTABLE_COLUMNS, 'first_name', 'asc');

        $executors = $executorsQuery->paginate(10);

        return view('V1.AdminBranch.Executors.index', compact('executors', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        $serviceAreas = ServiceArea::where('branch_id', session('branch')->id)->orderBy('name')->pluck('name', 'id');
        return view('V1.AdminBranch.Executors.create', compact('back_url', 'serviceAreas'));
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $executor = Employee::with('serviceAreas')->find($id);
        $serviceAreas = ServiceArea::where('branch_id', session('branch')->id)->orderBy('name')->pluck('name', 'id');
        $selectedServiceAreaIds = $executor->serviceAreas->pluck('id')->toArray();
        return view('V1.AdminBranch.Executors.edit', compact('back_url', 'executor', 'serviceAreas', 'selectedServiceAreaIds'));
    }

    public function store(EmployeeRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    // public function update(EmployeeRequest $request, string $id)
    public function update(EmployeeRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(Request $request, string $id = null)
    {
        try {
            // Crear o actualizar empleado
            $item = $id ? Employee::find($id) : new Employee();

            $item->identification_number = $request->input('identification_number');
            $item->first_name = $request->input('first_name');
            $item->last_name = $request->input('last_name');
            $item->phone_number = $request->input('phone_number');
            $item->address = $request->input('address');
            $item->external = $request->input('external');
            $item->executor = 1;
            $item->branch_id = session('branch')->id;
            $item->save();

            // Sincronizar la relación Muchos a Muchos con áreas de servicio
            $serviceAreaIds = $request->input('service_area_id', []);
            $item->serviceAreas()->sync($serviceAreaIds);

            // Respuesta AJAX
            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $item]);
            }

            // Redirección si viene back_url
            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            $action_msg = $id ? 'actualizado' : 'creado';
            $message = "Proveedor {$action_msg}: " . $item->first_name;
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage());
            return $this->alertError(self::INDEX, "Error al {$action} el proveedor: " . $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = Employee::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Proveedor eliminado: ' . $item->first_name . ' ' . $item->last_name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
