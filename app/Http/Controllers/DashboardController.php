<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FaultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Convierte una fecha de formato DD-MM-YYYY a YYYY-MM-DD.
     * Si el formato es incorrecto, intenta devolver la fecha original.
     *
     * @param string $dateString
     * @return string|null
     */
    protected function convertToMysqlDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Intenta parsear el formato DD-MM-YYYY
            $date = Carbon::createFromFormat('d-m-Y', $dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            // Si falla (ej: ya estaba en YYYY-MM-DD), lo devuelve tal cual.
            return $dateString;
        }
    }

    public function index(Request $request)
    {
        /** @var User $session */ //Esto es para que no aparezca el warning en el metodo getRoleNames()
        $session = auth()->user();
        $roleNames = $session->getRoleNames();
        $firstRoleName = $roleNames->first();

        if ($firstRoleName == 'Operador') {
            return redirect()->route('admin.sucursal.faults.index');
        }

        $userId = $session->id;

        // 1. Capturar los valores de los inputs.
        $fromInput = $request->input('from_date');
        $toInput = $request->input('to_date');

        // 🟢 LÓGICA DE REDIRECCIÓN EN PRIMERA CARGA
        // Si no vienen fechas en la URL, es la primera entrada. Redireccionar con el rango por defecto.
        if (empty($fromInput) && empty($toInput)) {

            // Calculamos las fechas por defecto en el formato que el usuario ve (DD-MM-YYYY)
            // $default_from_date = Carbon::now()->subDays(7)->format('d-m-Y');
            $default_from_date = '01-10-2025';
            $default_to_date = Carbon::now()->format('d-m-Y');

            // Redirigimos a la misma ruta con los parámetros en la URL.
            // Esto hará que el controlador se ejecute de nuevo, pero con fechas.
            return redirect()->route('dashboard', [
                'from_date' => $default_from_date,
                'to_date' => $default_to_date
            ]);
        }

        // 2. CONVERSIÓN DE FECHAS A FORMATO MYSQL
        // Si llegamos a este punto, $fromInput y $toInput ya tienen valores (ya sea por filtro o por la redirección).
        // Convertimos el valor de entrada (que puede ser DD-MM-YYYY o YYYY-MM-DD) a formato MySQL YYYY-MM-DD.
        $from = $this->convertToMysqlDate($fromInput);
        $to = $this->convertToMysqlDate($toInput);

        // Nota: La lógica anterior de "ESTABLECER VALORES POR DEFECTO" se elimina, ya que la redirección lo cubre.

        // ... (Tu lógica de usuario y sucursales sin cambios) ...

        $user = User::with('branches')->where('id', $userId)->first();
        if (isset($user->branches) && $user->branches->count() > 0) {
            //Es un usuario admin de una sucursal.
        } else {
            //Es un administrador general. Puede ver todas las sucursales
        }

        // Llamadas a FaultService usando $from y $to (ambos en formato MySQL YYYY-MM-DD)
        // El resto del código de gráficos y servicios se mantiene igual, usando las variables $from y $to
        // que ahora tienen valores garantizados y en formato MySQL.
        $mostFailingEquipment = FaultService::mostFailingEquipment($from, $to);
        $mostFailReported = FaultService::mostFailReported($from, $to);
        $totalActiveFaults = FaultService::totalActiveFaults($from, $to);
        $totalClosedFaults = FaultService::totalClosedFaults($from, $to);

        $failuresByServiceArea = FaultService::failuresByServiceArea($from, $to);

        $failuresByProject = FaultService::failuresByProject($from, $to);
        $failuresByReporter = FaultService::failuresByReporter($from, $to);
        $failuresByEquipment = FaultService::failuresByEquipment($from, $to);
        $failuresByStatus = FaultService::failuresByStatus($from, $to);
        $failuresBySparePartStatus = FaultService::failuresBySparePartStatus($from, $to);
        $faultsByStatus = FaultService::faultsByStatus($from, $to);
        $failuresByDivision = FaultService::failuresByDivision($from, $to);

        return view('dashboard', compact(
            'mostFailingEquipment',
            'mostFailReported',
            'totalActiveFaults',
            'totalClosedFaults',
            'failuresByServiceArea',
            'failuresByProject',
            'failuresByReporter',
            'failuresByEquipment',
            'failuresByStatus',
            'failuresBySparePartStatus',
            'faultsByStatus',
            'failuresByDivision',
        ));
    }
}
