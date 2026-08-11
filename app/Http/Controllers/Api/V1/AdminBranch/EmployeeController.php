<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminBranch\EmployeeRequest;
use App\Models\Employee;
use App\Services\UserService;
use App\Traits\Api\ApiResponse;
use App\Traits\Sortable;
use Illuminate\Http\Request;

/**
 * Spec §6.2.3. Reutiliza App\Services\UserService (ya existente en el
 * proyecto) para crear/actualizar el usuario de sistema vinculado, igual
 * que el controlador web equivalente.
 */
class EmployeeController extends Controller
{
    use ApiResponse;
    use Sortable;

    const SORTABLE_COLUMNS = ['id', 'identification_number', 'first_name', 'last_name', 'email', 'phone_number', 'address', 'position'];

    public function __construct()
    {
        $base = 'Empleados';
        $this->middleware("permission:{$base} Crear")->only(['store']);
        $this->middleware("permission:{$base} Editar")->only(['update']);
        $this->middleware("permission:{$base} Eliminar")->only(['destroy']);
        $this->middleware("permission:{$base} Ver")->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Employee::query()
            ->with(['users.roles'])
            ->where('branch_id', BranchHelper::getBranchId())
            ->where('external', 0);

        if ($search = $request->query('query')) {
            $query->where(function ($q) use ($search) {
                $q->where('identification_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        return $this->paginatedResponse($query->paginate(10));
    }

    public function show(string $id)
    {
        $employee = Employee::with(['users.roles'])
            ->where('id', $id)
            ->where('branch_id', BranchHelper::getBranchId())
            ->first();

        if (!$employee) {
            return $this->error('Empleado no encontrado.', 404);
        }

        return $this->success(['back_url' => null, 'employee' => $employee]);
    }

    public function store(EmployeeRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(EmployeeRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    protected function saveOrUpdate(EmployeeRequest $request, ?string $id = null)
    {
        $branchId = BranchHelper::getBranchId();
        $item = $id
            ? Employee::where('id', $id)->where('branch_id', $branchId)->first()
            : new Employee();

        if ($id && !$item) {
            return $this->error('Empleado no encontrado.', 404);
        }

        $item->identification_number = $request->input('identification_number');
        $item->first_name = $request->input('first_name');
        $item->last_name = $request->input('last_name');
        $item->email = $request->input('email');
        $item->phone_number = $request->input('phone_number');
        $item->address = $request->input('address');
        $item->position = $request->input('position');
        $item->executor = (int) $request->input('executor', 0);
        $item->external = 0;
        $item->branch_id = $branchId;
        $item->save();

        $roleId = $request->input('role_id');
        $password = $request->input('password');
        $linkedUser = null;

        if ($item->email && $roleId) {
            $user = \App\Models\User::where('email', $item->email)->first();

            $userData = [
                'name' => $item->last_name . ' ' . $item->first_name,
                'email' => $item->email,
                'phone' => $item->phone_number,
                'branchId' => $branchId,
                'roleId' => $roleId,
            ];

            if ($password) {
                $userData['password'] = $password;
            }

            if ($user) {
                $linkedUser = UserService::updateUser($user, $userData);
            } else {
                if (!isset($userData['password'])) {
                    return $this->error('Debe proporcionar un password para crear un nuevo usuario.', 422);
                }
                $linkedUser = UserService::insertUserRole($userData);
            }
        }

        $item->users()->sync($linkedUser ? [$linkedUser->id] : []);

        $status = $id ? 200 : 201;

        return $this->success($item, $id ? 'Empleado actualizado exitosamente.' : 'Empleado creado exitosamente.', $status);
    }

    public function destroy(string $id)
    {
        $item = Employee::where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$item) {
            return $this->error('Empleado no encontrado.', 404);
        }

        $item->delete();

        return $this->success(null, 'Empleado eliminado exitosamente.');
    }
}
