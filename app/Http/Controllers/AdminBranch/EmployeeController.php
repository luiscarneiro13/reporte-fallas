<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeEditRequest;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use App\Models\UserBranch;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.usuarios.employees.index";

    public function index()
    {
        $query = request('query');
        $employees = User::query()
            ->leftJoin('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', 'roles.id')
            ->leftJoin('user_branch', 'users.id', 'user_branch.user_id')
            ->leftJoin('branches', 'user_branch.branch_id', 'branches.id')
            ->select('users.id', 'users.name', 'users.email', 'users.phone', 'roles.name as rol', 'branches.id as branch_id', 'branches.name as branch')
            ->where('roles.name', 'Vendedor')
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
        return view('AdminBranch.Usuarios.Employees.index', compact('employees'));
    }

    public function create()
    {
        return view('AdminBranch.Usuarios.Employees.create');
    }

    public function store(EmployeeRequest $request)
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

            $rol = Role::where('name', "Vendedor")->first();
            $user->roles()->sync([$rol->id]);

            $userBranch = new UserBranch();
            $userBranch->branch_id = session('branch')->id;
            $user->userBranches()->save($userBranch);

            return $this->alertSuccess(self::INDEX, 'Vendedor creado: ' . $user->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit($id)
    {
        $employee = User::find($id);
        return view('AdminBranch.Usuarios.Employees.edit', compact('employee'));
    }

    public function update(EmployeeEditRequest $request, $id)
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
            return $this->alertSuccess(self::INDEX, 'Vendedor actualizado: ' . $user->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            return $this->alertSuccess(self::INDEX, 'Vendedor eliminado: ' . $user->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
