<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Exports\EmployeeDataExport;
use App\Exports\EmployeeIncidentsExport;
use App\Exports\EmployeesExport;
use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EmployeeRequest;
use App\Models\Cargo;
use App\Models\ContractType;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use App\Services\UserService;
use App\Traits\AlertResponser;
use App\Traits\DateTransformerTrait;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    use AlertResponser;
    use Sortable;
    use DateTransformerTrait;

    const INDEX = "admin.sucursal.employees.index";

    const SORTABLE_COLUMNS = [
        'identification_number',
        'phone_number',
        'cargo_id',
        'address',
    ];

    public function __construct()
    {
        $basePermission = "Empleados";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $result = $this->getFilteredEmployeesQuery($request);

        $employees = $result['query']->paginate(10)->appends($request->query());
        $sortBy = $result['sort_by'];
        $sortDir = $result['sort_dir'];

        return view('V1.AdminBranch.Employees.index', compact('employees', 'sortBy', 'sortDir'));
    }

    /**
     * Exporta el listado de todos los empleados
     */
    public function impAll(Request $request)
    {
        $back_url = request()->back_url ?? null;

        // Reutiliza los mismos filtros (sucursal, búsqueda) que el listado, sin paginación
        $result = $this->getFilteredEmployeesQuery($request);
        $employees = $result['query']->get();

        return view('V1.AdminBranch.Employees.impAll', compact('back_url', 'employees'));
    }

    /**
     * Exporta el listado de todos los empleados a un excel
     */
    public function excel(Request $request)
    {
        $result = $this->getFilteredEmployeesQuery($request);
        $employees = $result['query']->with(['fichaIngreso', 'projects', 'contractType'])->get();

        return Excel::download(new EmployeesExport($employees), 'empleados.xlsx');
    }

    private function getFilteredEmployeesQuery(Request $request): array
    {
        $query = $request->query('query');
        $employeesQuery = Employee::with(['users.roles', 'cargo'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('identification_number', 'like', "%{$query}%")
                        ->orWhere('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('phone_number', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%")
                        ->orWhere('position', 'like', "%{$query}%")
                    ;
                });
            })
            ->where('branch_id', session('branch')->id)
            ->where('external', 0);

        [$sortBy, $sortDir] = $this->applySort($employeesQuery, $request, self::SORTABLE_COLUMNS, 'id', 'desc');

        return [
            'query' => $employeesQuery,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ];
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        $rolesCollection = Role::where('name', '!=', 'Super Admin')->get()->pluck('name', 'id');
        $roles = $rolesCollection->prepend('Sin usuario de sistema', '0');
        $projectsCollection = Project::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $projects = $projectsCollection->prepend('Sin proyecto', '0');
        $contractTypesCollection = ContractType::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $contractTypes = $contractTypesCollection->prepend('Sin tipo de contrato', '0');
        $cargosCollection = Cargo::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $cargos = $cargosCollection->prepend('Sin cargo', '0');
        return view('V1.AdminBranch.Employees.create', compact('back_url', 'roles', 'projects', 'contractTypes', 'cargos'));
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $employee = Employee::with(['users.roles', 'projects', 'fichaIngreso'])->find($id);
        $rolesCollection = Role::where('name', '!=', 'Super Admin')->get()->pluck('name', 'id');
        $roles = $rolesCollection->prepend('Sin usuario de sistema', '0');
        $userSystem = $employee->users->first() ?? null;
        $projectsCollection = Project::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $projects = $projectsCollection->prepend('Sin proyecto', '0');
        $contractTypesCollection = ContractType::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $contractTypes = $contractTypesCollection->prepend('Sin tipo de contrato', '0');
        $cargosCollection = Cargo::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $cargos = $cargosCollection->prepend('Sin cargo', '0');
        $incidents = $employee->incidents()->with('reportedBy')->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10);
        $employmentPeriods = $employee->employmentPeriods()->with(['contractType', 'cargo'])->get();
        return view('V1.AdminBranch.Employees.edit', compact('back_url', 'employee', 'roles', 'userSystem', 'projects', 'contractTypes', 'cargos', 'incidents', 'employmentPeriods'));
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

    private function saveOrUpdate(EmployeeRequest $request, string $id = null)
    {
        try {
            // Crear o actualizar empleado
            $item = $id ? Employee::find($id) : new Employee();
            $item->identification_number = $request->input('identification_number');
            $item->first_name = $request->input('first_name');
            $item->last_name = $request->input('last_name');
            $item->email = $request->input('email');
            $item->phone_number = $request->input('phone_number');
            $item->address = $request->input('address');
            $item->executor = $request->input('executor');
            $item->cargo_id = $request->input('cargo_id') ?: null;
            $item->branch_id = session('branch')->id;

            $dateData = $this->transformDateFields($request->only('hire_date'), ['hire_date']);
            $item->hire_date = $dateData['hire_date'];
            $item->contract_type_id = $request->input('contract_type_id') ?: null;

            $item->save();

            // Sincronizar la relación Muchos a Muchos con proyectos (mismo patrón que Equipment)
            $projectIds = $request->input('project_id');
            if (!is_array($projectIds)) {
                $projectIds = $projectIds ? [$projectIds] : [];
            }
            $item->projects()->sync($projectIds);

            // Ficha de ingreso (relación 1 a 1)
            $fichaData = $this->transformDateFields($request->only('birth_date'), ['birth_date']);
            $fichaData = [
                'birth_date' => $fichaData['birth_date'],
                'nationality' => $request->input('nationality'),
                'has_driver_license' => $request->boolean('has_driver_license'),
                'driver_license_grade' => $request->input('driver_license_grade'),
                'account_number' => $request->input('account_number'),
                'account_type' => $request->input('account_type'),
                'bank' => $request->input('bank'),
                'has_occupational_certificate' => $request->boolean('has_occupational_certificate'),
                'shirt_size' => $request->input('shirt_size'),
                'coverall_size' => $request->input('coverall_size'),
                'shoe_size' => $request->input('shoe_size'),
                'emergency_contact_name' => $request->input('emergency_contact_name'),
                'emergency_contact_phone' => $request->input('emergency_contact_phone'),
            ];

            if ($request->hasFile('photo')) {
                $images = new Images();
                $fichaData['photo'] = $images->uploadImage($request->file('photo'), 'employees');
            }

            $item->fichaIngreso()->updateOrCreate(['employee_id' => $item->id], $fichaData);

            // Datos de usuario
            $roleId = $request->input('role_id');
            $password = $request->input('password');
            $linkedUser = null; // Variable para almacenar el User creado o encontrado

            if ($item->email && $roleId) {
                // Buscar usuario por email
                $user = User::where('email', $item->email)->first();

                $userData = [
                    'name' => $item->last_name . ' ' . $item->first_name,
                    'email' => $item->email,
                    'phone' => $item->phone_number,
                    'branchId' => $item->branch_id,
                    'roleId' => $roleId,
                ];

                // Solo asignar contraseña si viene
                if ($password) {
                    $userData['password'] = $password;
                }

                if ($user) {
                    // Actualizar usuario existente
                    // UserService::updateUser debe devolver el objeto User actualizado
                    $linkedUser = UserService::updateUser($user, $userData);
                } else {
                    // Crear usuario nuevo
                    if (!isset($userData['password'])) {
                        throw new \Exception("Debe proporcionar un password para crear un nuevo usuario.");
                    }
                    // UserService::insertUserRole debe crear el usuario, asignar el rol, y devolver el objeto User
                    $linkedUser = UserService::insertUserRole($userData);
                }
            }

            // 🚀 VINCULACIÓN CRÍTICA: Asocia el empleado con el usuario
            if ($linkedUser) {
                // Utiliza sync() para asegurar que solo este usuario esté vinculado a este empleado
                // en la tabla pivote 'employee_users'.
                $item->users()->sync([$linkedUser->id]);
            } else {
                // Si se eliminó el rol o el email, desvincular al usuario del empleado
                $item->users()->sync([]);
            }

            // Respuesta AJAX
            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $item]);
            }

            // Redirección si viene back_url
            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            $action_msg = $id ? 'actualizado' : 'creado';
            $message = "Empleado {$action_msg}: " . $item->first_name;
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage());
            return $this->alertError(self::INDEX, "Error al {$action} el empleado: " . $th->getMessage());
        }
    }

    public function incidents(Employee $employee)
    {
        $employee->load(['projects', 'contractType', 'users.roles', 'cargo']);
        $incidents = $employee->incidents()->with('reportedBy')->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10);

        return view('V1.AdminBranch.Employees.incidents', compact('employee', 'incidents'));
    }

    /**
     * Exporta todos los datos del empleado a un excel
     */
    public function exportData(Employee $employee)
    {
        $employee->load(['fichaIngreso', 'cargo', 'projects', 'contractType', 'users.roles']);

        return Excel::download(new EmployeeDataExport($employee), 'empleado-' . $employee->identification_number . '.xlsx');
    }

    /**
     * Exporta todas las incidencias del empleado a un excel
     */
    public function exportIncidents(Employee $employee)
    {
        $incidents = $employee->incidents()->with(['employee', 'reportedBy'])->orderBy('date', 'desc')->orderBy('id', 'desc')->get();

        return Excel::download(new EmployeeIncidentsExport($incidents), 'incidencias-' . $employee->identification_number . '.xlsx');
    }

    public function destroy(string $id)
    {
        try {
            $item = Employee::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Empleado eliminado: ' . $item->first_name . ' ' . $item->last_name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
