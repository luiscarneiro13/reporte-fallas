<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UserRequest;
use App\Models\User;
use App\Models\UserBranch;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SupervisorController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.usuarios.supervisors.index";

    public function __construct()
    {
        $basePermission = "Supervisores";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
        $this->middleware('permission:' . $basePermission . ' Ver')->except(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $query = request('query');
        $supervisors = User::query()
            ->leftJoin('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', 'roles.id')
            ->leftJoin('user_branch', 'users.id', 'user_branch.user_id')
            ->leftJoin('branches', 'user_branch.branch_id', 'branches.id')
            ->select('users.id', 'users.name', 'users.email', 'users.phone', 'roles.name as rol', 'branches.id as branch_id', 'branches.name as branch')
            ->where('roles.name', 'Supervisor')
            ->where('branches.id', session('branch')->id)
            ->where('users.id', '!=', auth()->user()->id)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('users.name', 'like', "%{$query}%")
                        ->orWhere('users.email', 'like', "%{$query}%")
                        ->orWhere('users.phone', 'like', "%{$query}%");
                });
            })
            ->orderBy('users.name', 'asc')
            ->paginate(10);
        return view('AdminBranch.Usuarios.Supervisors.index', compact('supervisors'));
    }

    public function create()
    {
        return view('AdminBranch.Usuarios.Supervisors.create');
    }

    public function store(UserRequest $request)
    {
        try {
            $user = User::create([
                "name" => $request->input('name'),
                "email" => $request->input('email'),
                "phone" => $request->input('phone') ?? null,
                "password" => hash::make($request->input('password')),
                "email_verified_at" => now(),
                "profile_photo_path" => 'images/user-icon.webp',
            ]);

            $rol = Role::where('name', "Supervisor")->first();
            $user->roles()->sync([$rol->id]);

            $userBranch = new UserBranch();
            $userBranch->branch_id = session('branch')->id;
            $user->userBranches()->save($userBranch);

            return $this->alertSuccess(self::INDEX, 'Supervisor creado: ' . $user->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit($id)
    {
        $supervisor = User::find($id);
        return view('AdminBranch.Usuarios.Supervisors.edit', compact('supervisor'));
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $user = User::find($id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone') ?? null;
            $password = $request->input('password');

            if ($password) {
                $user->password = hash::make($request->input('password'));
            }

            $user->save();
            return $this->alertSuccess(self::INDEX, 'Supervisor actualizado: ' . $user->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            return $this->alertSuccess(self::INDEX, 'Supervisor eliminado: ' . $user->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
