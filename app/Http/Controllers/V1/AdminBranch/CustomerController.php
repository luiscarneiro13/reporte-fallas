<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    use AlertResponser;

    const INDEX = "admin.sucursal.customers.index";

    public function index()
    {
        $query = request('query');
        $customers = Customer::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                    $subQuery->Orhere('address', 'like', "%{$query}%");
                    $subQuery->Orhere('email', 'like', "%{$query}%");
                    $subQuery->Orhere('phone', 'like', "%{$query}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('V1.AdminBranch.Customers.index', compact('customers'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('V1.AdminBranch.Customers.create', compact('back_url'));
    }

    public function store(Request $request)
    {
        try {
            $item = new Customer();
            $item->name = $request->input('name');
            $item->address = $request->input('address');
            $item->email = $request->input('email');
            $item->phone = $request->input('phone');
            $item->branch_id = session('branch')->id;
            $item->save();

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Cliente creado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $customer = Customer::find($id);
        return view('V1.AdminBranch.Customers.edit', compact('customer', 'back_url'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $item = Customer::find($id);
            $item->name = $request->input('name');
            $item->address = $request->input('address');
            $item->email = $request->input('email');
            $item->phone = $request->input('phone');
            $item->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Cliente actualizado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = Customer::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Cliente eliminado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
