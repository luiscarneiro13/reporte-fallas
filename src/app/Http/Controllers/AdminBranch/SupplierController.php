<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.suppliers.index";

    public function index()
    {
        $query = request('query');
        $suppliers = Supplier::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('AdminBranch.Suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('AdminBranch.Suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'address' => 'string|min:3',
            'phone' => 'string|min:3',
            'email' => 'string|min:3',
        ]);

        try {
            $supplier = new Supplier();
            $supplier->branch_id = session('branch')->id;
            $supplier->name = $request->input('name');
            $supplier->address = $request->input('address');
            $supplier->phone = $request->input('phone');
            $supplier->email = $request->input('email');
            $supplier->save();
            if (request()->back_url) {
                return redirect(request()->back_url);
            }
            return $this->alertSuccess(self::INDEX, 'Proveedor guardado: ' . $supplier->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $supplier = Supplier::find($id);
        return view('AdminBranch.Suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $supplier = Supplier::find($id);
            $supplier->name = $request->input('name');
            $supplier->address = $request->input('address');
            $supplier->phone = $request->input('phone');
            $supplier->email = $request->input('email');
            $supplier->save();
            if (request()->back_url) {
                return redirect(request()->back_url);
            }
            return $this->alertSuccess(self::INDEX, 'Proveedor actualizado: ' . $supplier->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $supplier = Supplier::find($id);
            $supplier->delete();
            if (request()->back_url) {
                return redirect(request()->back_url);
            }
            return $this->alertSuccess(self::INDEX, 'Proveedor eliminado: ' . $supplier->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
