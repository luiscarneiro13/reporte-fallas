<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FaultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $session = auth()->user();
        $userId = $session->id;

        $user = User::with('branches')->where('id', $userId)->first();
        // return $user->branches->count();
        if (isset($user->branches) && $user->branches->count() > 0) {
            //Es un usuario admin de una sucursal.
            //Entonces se agregan los datos de la sucursal en la sesión. Para usar en toda la aplicación.
        } else {
            //Es un administrador general. Puede ver todas las sucursales
        }

        $mostFailingEquipment = FaultService::mostFailingEquipment();
        $mostFailReported = FaultService::mostFailReported();
        $totalActiveFaults = FaultService::totalActiveFaults();
        $totalClosedFaults = FaultService::totalClosedFaults();

        return view('dashboard', compact('mostFailingEquipment', 'mostFailReported', 'totalActiveFaults', 'totalClosedFaults'));
    }
}
