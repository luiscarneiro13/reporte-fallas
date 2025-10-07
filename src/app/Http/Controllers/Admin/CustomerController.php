<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    use AlertResponser;
    const INDEX = "customers.index";
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        // $this->middleware('can: Crear Cliente')->only('create');
        // $this->middleware('can: Editar Cliente')->only('edit');
        // $this->middleware('can: Eliminar Cliente')->only('destroy');
    }

    public function index()
    {
        $customers = Customer::all();
        return view('Customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|unique:customers,dni|numeric|min:3',
            'nombre' => 'required|string|min:3',
            'apellido' => 'required|string|min:3',
            'email' => 'required|email|unique:customers,email|min:3|',
            'telefono' => 'required|numeric|min:3',
        ]);

        try {
            $customer = new Customer();

            $customer->dni = $request->input('dni');
            $customer->nombre = $request->input('nombre');
            $customer->apellido = $request->input('apellido');
            $customer->email = $request->input('email');
            $customer->telefono = $request->input('telefono');
            $customer->direccion = $request->input('direccion');
            $customer->estado = $request->input('estado');

            $customer->save();

            return $this->alertSuccess(self::INDEX, 'Usuario ' . $customer->nombre . ' ' . $customer->apellido . ' guardado');
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::find($id);
        return view('Customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'dni' => 'required|unique:customers,dni|numeric|min:3',
            'nombre' => 'required|string|min:3',
            'apellido' => 'required|string|min:3',
            'email' => 'required|email|unique:customers,email|min:3|',
            'telefono' => 'required|numeric|min:3',
        ]);

        try {
            $customer = Customer::find($id);

            $customer->dni = $request->input('dni');
            $customer->nombre = $request->input('nombre');
            $customer->apellido = $request->input('apellido');
            $customer->email = $request->input('email');
            $customer->telefono = $request->input('telefono');
            $customer->direccion = $request->input('direccion');
            $customer->estado = $request->input('estado');

            $customer->save();

            return $this->alertSuccess(self::INDEX, 'Usuario ' . $customer->nombre . ' ' . $customer->apellido . ' actualizado');
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return back();
    }
}
