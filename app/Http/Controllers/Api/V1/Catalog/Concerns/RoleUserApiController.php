<?php

namespace App\Http\Controllers\Api\V1\Catalog\Concerns;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\UserRoleRequest;
use App\Models\User;
use App\Models\UserBranch;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Base para Operadores/Supervisores/Administradores (spec §6.3.7-6.3.9):
 * actúan sobre App\Models\User filtrando por rol Spatie, con filas planas
 * en el índice (join manual, sin exponer el modelo User completo).
 */
abstract class RoleUserApiController extends Controller
{
    use ApiResponse;

    protected string $roleName;
    protected string $permissionBase;
    protected string $resourceName;

    public function __construct()
    {
        $base = $this->permissionBase;
        $this->middleware("permission:{$base} Crear")->only(['store']);
        $this->middleware("permission:{$base} Editar")->only(['update']);
        $this->middleware("permission:{$base} Eliminar")->only(['destroy']);
        $this->middleware("permission:{$base} Ver")->only(['index', 'edit']);
    }

    public function index(Request $request)
    {
        $branchId = BranchHelper::getBranchId();
        $search = $request->query('query');

        $query = User::query()
            ->leftJoin('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', 'roles.id')
            ->leftJoin('user_branch', 'users.id', 'user_branch.user_id')
            ->leftJoin('branches', 'user_branch.branch_id', 'branches.id')
            ->select('users.id', 'users.name', 'users.email', 'users.phone', 'roles.name as rol', 'branches.id as branch_id', 'branches.name as branch')
            ->where('roles.name', $this->roleName)
            ->where('branches.id', $branchId)
            ->where('users.id', '!=', $request->user()->id)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('users.phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('users.name', 'asc');

        return $this->paginatedResponse($query->paginate(10));
    }

    public function edit(string $id)
    {
        $user = User::where('id', $id)
            ->whereHas('branches', fn ($q) => $q->where('branches.id', BranchHelper::getBranchId()))
            ->first();

        if (!$user) {
            return $this->error("{$this->resourceName} no encontrado.", 404);
        }

        return $this->success($user);
    }

    public function store(UserRoleRequest $request)
    {
        $branchId = BranchHelper::getBranchId();

        if (!$branchId) {
            return $this->error('El usuario autenticado no tiene una sucursal asociada.', 400);
        }

        $user = DB::transaction(function () use ($request, $branchId) {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
                'email_verified_at' => now(),
                'profile_photo_path' => 'images/user-icon.webp',
            ]);

            $role = Role::where('name', $this->roleName)->where('guard_name', 'sanctum')->first();
            $user->roles()->sync([$role->id]);

            $userBranch = new UserBranch();
            $userBranch->branch_id = $branchId;
            $user->userBranches()->save($userBranch);

            return $user;
        });

        return $this->created($user->fresh(), "{$this->resourceName} creado exitosamente.");
    }

    public function update(UserRoleRequest $request, string $id)
    {
        $user = User::where('id', $id)
            ->whereHas('branches', fn ($q) => $q->where('branches.id', BranchHelper::getBranchId()))
            ->first();

        if (!$user) {
            return $this->error("{$this->resourceName} no encontrado.", 404);
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');

        if ($password = $request->input('password')) {
            $user->password = Hash::make($password);
        }

        $user->save();

        return $this->success($user, "{$this->resourceName} actualizado exitosamente.");
    }

    public function destroy(string $id)
    {
        $user = User::where('id', $id)
            ->whereHas('branches', fn ($q) => $q->where('branches.id', BranchHelper::getBranchId()))
            ->first();

        if (!$user) {
            return $this->error("{$this->resourceName} no encontrado.", 404);
        }

        $user->delete();

        return $this->success(null, "{$this->resourceName} eliminado exitosamente.");
    }
}
