<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductEntry;
use App\Models\Supplier;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class ProductEntryController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.product.entries.index";

    public function index()
    {
        $query = request('query');
        $entries = ProductEntry::query()
            ->leftJoin('products', 'product_entries.product_id', 'products.id')
            ->leftJoin('suppliers', 'product_entries.supplier_id', 'suppliers.id')
            ->leftJoin('users', 'product_entries.user_id', 'users.id')
            ->where('products.branch_id', session('branch')->id)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('products.name', 'like', "%{$query}%")
                        ->orWhere('suppliers.name', 'like', "%{$query}%")
                        ->orWhere('product_entries.entry_qty', 'like', "%{$query}%")
                        ->orWhere('product_entries.updated_at', 'like', "%{$query}%")
                        ->orWhere('product_entries.purchase_price', 'like', "%{$query}%")
                        ->orWhere('product_entries.selling_price', 'like', "%{$query}%");
                });
            })
            ->select(
                'product_entries.id',
                'products.id as product_id',
                'products.name as product',
                'suppliers.name as supplier',
                'product_entries.entry_qty',
                'product_entries.updated_at as date_ingress',
                'product_entries.purchase_price',
                'product_entries.selling_price'
            )
            ->distinct('products.id')
            ->orderBy('product_entries.id', 'desc')
            ->paginate(10);

        return view('AdminBranch.ProductEntries.index', compact('entries'));
    }

    public function create()
    {
        $products = Product::where('branch_id', session('branch')->id)->get()->mapWithKeys(function ($product) {
            return [$product->id => $product->name];
        });
        $suppliers = Supplier::where('branch_id', session('branch')->id)->get()->mapWithKeys(function ($type) {
            return [$type->id => $type->name];
        });
        return view('AdminBranch.ProductEntries.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Ingresar en la tabla de ingresos
        try {
            $entry = new ProductEntry();
            $entry->branch_id = session('branch')->id;
            $entry->product_id = $request->input('product_id');
            $entry->supplier_id = $request->input('supplier_id');
            $entry->user_id = auth()->user()->id;
            $entry->entry_qty = $request->input('entry_qty');
            $entry->purchase_price = $request->input('purchase_price');
            $entry->selling_price = $request->input('selling_price');
            $entry->save();

            $product = Product::find($request->product_id);
            $product->available_qty = (int)$product->available_qty + (int)$request->entry_qty;
            $product->price = $request->input('selling_price');
            $product->save();

            return $this->alertSuccess(self::INDEX, 'Producto ingresado: ' . $entry->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
        //Buscar el producto
        $product = Product::find($request->input('product_id'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $products = Product::where('branch_id', session('branch')->id)->get()->mapWithKeys(function ($product) {
            return [$product->id => $product->name];
        });

        $suppliers = Supplier::where('branch_id', session('branch')->id)->get()->mapWithKeys(function ($type) {
            return [$type->id => $type->name];
        });

        $entry = ProductEntry::query()
            ->leftJoin('products', 'product_entries.product_id', 'products.id')
            ->select('product_entries.*', 'products.name as product')
            ->find($id);

        return view('AdminBranch.ProductEntries.edit', compact('entry', 'products', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $entry = ProductEntry::find($id);
            $oldQty = $entry->entry_qty;
            $entry->branch_id = session('branch')->id;
            $entry->product_id = $request->input('product_id');
            $entry->supplier_id = $request->input('supplier_id');
            $entry->user_id = auth()->user()->id;
            $entry->entry_qty = $request->input('entry_qty');
            $entry->purchase_price = $request->input('purchase_price');
            $entry->selling_price = $request->input('selling_price');
            $entry->save();

            $product = Product::find($request->input('product_id'));
            $product->available_qty = ((int)$product->available_qty - (int)$oldQty) + $request->input('entry_qty');
            $product->price = $request->input('selling_price');
            $product->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Entrada actualizada');
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $entry = ProductEntry::find($id);
            $oldQtyEntry = $entry->entry_qty;
            $product = Product::find($entry->product_id);
            $oldAvailableQtyProduct = (int)$product->available_qty - (int)$oldQtyEntry;
            $product->available_qty = $oldAvailableQtyProduct;
            $product->save();
            $entry->delete();
            return $this->alertSuccess(self::INDEX, 'Entrada eliminada');
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
