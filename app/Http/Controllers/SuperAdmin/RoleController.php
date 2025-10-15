<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    use AlertResponser;

    public function index()
    {
        $roles = Role::all();
        return view('SuperAdmin.Users.Roles.Index', compact('roles'));
    }

    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->input('nombre')]);
        return redirect()->route('roles.edit', $role);
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('SuperAdmin.Users.Roles.RolesPermissions', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $role->permissions()->sync($request->permissions);
        return redirect()->route('roles.index')->with(['state' => 'success', 'message' => 'Se agregaron permisos para el rol ' . $role->name]);
    }

    public function destroy(string $id)
    {
        $customer = Role::find($id);
        $customer->delete();
        return back();
    }
}
