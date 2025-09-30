<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AssignRoleController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('SuperAdmin.Users.UserRoles.Index', compact('users'));
    }

    public function edit(string $id)
    {
        $user = User::find($id);
        $roles = Role::all();
        return view('SuperAdmin.Users.UserRoles.UserRoles', compact('user', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->roles()->sync($request->roles);
        return redirect()->route('assign_role.index', $user)->with(['state' => 'success', 'message' => 'Se agregaron roles al usuario ' . $user->name . ' ' . $user->last_name]);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('assign_role.index', $user)->with(['state' => 'success', 'message' => 'Se eliminó al usuario ' . $user->name . ' ' . $user->last_name]);
    }
}
