<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Branch;
use App\Models\Configuration;
use App\Models\DailyRate;
use App\Models\MethodPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{

    public function create()
    {
        $sucursales = Branch::get()->mapWithKeys(function ($sucursal) {
            return [$sucursal->id => $sucursal->name];
        });

        // El hosting compartido (LiteSpeed) cachea a nivel de servidor las páginas GET.
        // Si /login queda cacheado, todos los visitantes reciben el mismo token @csrf
        // "congelado" en el HTML, que ya no coincide con su sesión real -> error
        // "Page Expired" (419) al enviar el formulario. X-LiteSpeed-Cache-Control es el
        // header que LSCache respeta para excluir la respuesta de su caché de borde.
        return response()
            ->view('auth.login', compact('sucursales'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('X-LiteSpeed-Cache-Control', 'no-cache');
    }

    public function store(LoginRequest $request)
    {

        // $request->session()->flush();
        // return;
        $user = User::where('email', $request->email)->first();

        // Autentica al usuario
        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
            // El usuario ha sido autenticado correctamente
            $dailyRate = DailyRate::orderBy('id', 'desc')->first();
            Session::put('branch', Branch::find(1));
            Session::put('dailyRate', (float)$dailyRate->rate);
            Session::put('averageRate', (float)$dailyRate->average_rate);

            $config = Configuration::first();
            $tax = $config->tax ?? 16;
            Session::put('tax', (float)$tax);

            return redirect(url('dashboard'));
        } else {
            // Las credenciales son inválidas
            return redirect(url('/login'))->with(['state' => 'error', 'message' => 'Usuario o autorizado']);
        }

        $user->createToken('token')->plainTextToken;

        auth()->user = $user;

        return auth()->user();
    }
}
