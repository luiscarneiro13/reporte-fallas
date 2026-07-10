<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EmployeeIncidentEditRequest;
use App\Http\Requests\V1\EmployeeIncidentRequest;
use App\Models\Employee;
use App\Models\EmployeeIncident;
use App\Traits\AlertResponser;
use App\Traits\DateTransformerTrait;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeIncidentController extends Controller
{
    use AlertResponser;
    use DateTransformerTrait;
    use Sortable;

    const INDEX = "admin.sucursal.employee.incidents.index";
    const SORTABLE_COLUMNS = ['date'];

    public function __construct()
    {
        $basePermission = "Incidencias de Empleados";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = request('query');
        $incidentsQuery = EmployeeIncident::with(['employee', 'reportedBy'])
            ->where('branch_id', session('branch')->id)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('description', 'like', "%{$query}%")
                        ->orWhereHas('employee', function ($employeeQuery) use ($query) {
                            $employeeQuery->where('first_name', 'like', "%{$query}%")
                                ->orWhere('last_name', 'like', "%{$query}%")
                                ->orWhere('identification_number', 'like', "%{$query}%");
                        });
                });
            });

        [$sortBy, $sortDir] = $this->applySort($incidentsQuery, $request, self::SORTABLE_COLUMNS, 'date', 'desc');

        $incidents = $incidentsQuery->paginate(10);

        return view('V1.AdminBranch.EmployeeIncidents.index', compact('incidents', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        $selectedEmployeeId = request()->employee_id ?? null;
        $employees = $this->employeesForSelect()->prepend('Seleccione', '0');
        return view('V1.AdminBranch.EmployeeIncidents.create', compact('back_url', 'employees', 'selectedEmployeeId'));
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $incident = EmployeeIncident::find($id);
        $employees = $this->employeesForSelect();
        return view('V1.AdminBranch.EmployeeIncidents.edit', compact('incident', 'back_url', 'employees'));
    }

    public function store(EmployeeIncidentRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(EmployeeIncidentEditRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(Request $request, string $id = null)
    {
        try {
            $validatedData = $request->validated();
            $validatedData = $this->transformDateFields($validatedData, ['date']);

            $item = $id ? EmployeeIncident::find($id) : new EmployeeIncident();
            $item->employee_id = $validatedData['employee_id'];
            $item->date = $validatedData['date'];
            $item->description = $validatedData['description'];
            $item->branch_id = session('branch')->id;

            if (!$id) {
                $item->reported_by_id = auth()->id();
            }

            $item->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            $action_msg = $id ? 'actualizada' : 'creada';
            $message = "Incidencia de empleado {$action_msg}.";
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage());
            return $this->alertError(self::INDEX, "Error al {$action} la incidencia: " . $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = EmployeeIncident::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Incidencia de empleado eliminada.');
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    private function employeesForSelect()
    {
        return Employee::where('branch_id', session('branch')->id)
            ->select(
                'id',
                DB::raw("CONCAT(identification_number, ' - ', last_name, ' ', first_name) AS full_name")
            )
            ->orderBy('last_name', 'asc')
            ->pluck('full_name', 'id');
    }
}
