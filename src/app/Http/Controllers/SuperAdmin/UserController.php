<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AlertResponser;

    public function create()
    {
        return view('SuperAdmin.Users.Users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email|min:3',
            'password' => 'required|min:6',
            'password_confirmation' => 'required_with:password|same:password|min:6',
        ]);
        try {
            //Aquí usé User::create porque este modelo está trabajando con spatie y no deja hacer esto: new User()
            $user = User::create([
                "name" => $request->input('name'),
                "email" => $request->input('email'),
                "password" => hash::make($request->input('password')),
                "email_verified_at" => now(),
                "profile_photo_path" => 'images/user-icon.webp',
            ]);

            return redirect()->route('assign_role.edit', $user)->with(['state' => 'success', 'message' => 'Usuario ' . $user->name . ' guardado. Por favor agregue un rol']);
        } catch (\Throwable $th) {
            return $this->alertError($th);
        }
    }
}
