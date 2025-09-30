<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('SuperAdmin.Users.Permissions.Index', compact('permissions'));
    }

    public function store(Request $request)
    {
        Permission::create(['name' => $request->input('nombre')]);
        return back();
    }

    public function destroy(string $id)
    {
        $customer = Permission::find($id);
        $customer->delete();
        return back();
    }
}
