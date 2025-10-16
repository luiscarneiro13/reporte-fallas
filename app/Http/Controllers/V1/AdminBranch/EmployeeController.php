<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use App\Services\UserService;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.employees.index";

    public function index()
    {
        $query = request('query');
        $employees = Employee::with(['users.roles'])
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
            ->where('external', 0)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('V1.AdminBranch.Employees.index', compact('employees'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        $initValue = collect(["0" => "Sin usuario de sistema"]);
        $rolesCollection =  Role::get()->pluck('name', 'id');
        $roles = $initValue->merge($rolesCollection);
        return view('V1.AdminBranch.Employees.create', compact('back_url', 'roles'));
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $employee = Employee::find($id);
        $initValue = collect(["0" => "Sin usuario de sistema"]);
        $rolesCollection =  Role::get()->pluck('name', 'id');
        $roles = $initValue->merge($rolesCollection);
        return view('V1.AdminBranch.Employees.edit', compact('back_url', 'employee', 'roles'));
    }

    public function store(Request $request)
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
            $item->email = $request->input('email');
            $item->phone_number = $request->input('phone_number');
            $item->address = $request->input('address');
            $item->executor = $request->input('executor');
            $item->position = $request->input('position');
            $item->branch_id = session('branch')->id;
            $item->save();

            // Datos de usuario
            $roleId = $request->input('role_id');
            $password = $request->input('password');

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
                    UserService::updateUser($user, $userData);
                } else {
                    // Crear usuario nuevo
                    if (!isset($userData['password'])) {
                        throw new \Exception("Debe proporcionar un password para crear un nuevo usuario.");
                    }
                    UserService::insertUserRole($userData);
                }
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
