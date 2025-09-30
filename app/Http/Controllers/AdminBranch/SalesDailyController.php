<?php

namespace App\Http\Controllers\AdminBranch;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MethodPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class SalesDailyController extends Controller
{
    use AlertResponser;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = now()->startOfDay();
        $query = request()->query('query');

        $totales = DB::table('sales')
            ->join('method_payments', 'sales.method_payment_id', '=', 'method_payments.id')
            ->select('method_payments.name', DB::raw('SUM(sales.total_bs) as total_bs_sum'))
            ->groupBy('method_payments.name')
            ->where('sales.branch_id', session('branch')->id)
            ->where('sales.created_at', '>=', $today)
            ->whereNull('sales.cancel_sale')
            ->whereNotNull('sales.method_payment_id')
            ->get();

        $totalArticulos = Sale::query()
            ->where('branch_id', session('branch')->id)
            ->where('created_at', '>=', $today)
            ->whereNull('sales.cancel_sale')
            ->whereNotNull('sales.method_payment_id')
            ->sum('total_items');

        $salesQuery = Sale::where('branch_id', session('branch')->id)
            ->where('created_at', '>=', $today);

        if ($query) {

            $salesQuery->where(function ($q) use ($query) {
                $q->whereHas('customer', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })->orWhereHas('methodPayment', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })->orWhereHas('deliveryNotes', function ($q) use ($query) {
                    $q->where('id', $query);
                })->orWhereHas('invoices', function ($q) use ($query) {
                    $q->where('id', $query);
                });
            });

            // Filtrar por hora en PHP
            $salesQuery->orWhere(function ($q) use ($query) {
                $q->whereRaw("TIME_FORMAT(created_at, '%h:%i %p') LIKE ?", ['%' . $query . '%']);
            });
        }

        $sales = $salesQuery->orderBy('id', 'desc')->paginate(10);

        return view('AdminBranch.DailySales.index', compact('sales', 'totales', 'totalArticulos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sale = Sale::where('id', $id)->first();
        $methodPayments = MethodPayment::all();
        return view('AdminBranch.SalesHistory.show', compact('sale', 'methodPayments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    // Este método se usa para completar la venta cuando en la caja se registra el pago
    public function update(Request $request, string $id)
    {
        $sale = Sale::find($id);
        $sale->method_payment_id = (int)$request->input("method_payment_id");
        $sale->paid = 1;
        $sale->save();
        try {
            return response()->json(['success' => true, 'data' => Sale::find($id)]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);
        if (isset($sale->details) && count($sale->details) > 0) {
            foreach ($sale->details as $item) {
                $this->devolverProducto($item['product_service_id'], $item['qty']);
            }
        }
        $sale->cancel_sale = now();
        $sale->save();
        return redirect()->route('admin.sucursal.sales.history.show', $sale)->with('success', 'Venta anulada correctamente');
    }

    public function devolverProducto($productId, $qty)
    {
        try {
            $product = Product::find($productId);
            $product->available_qty = (int)$product->available_qty + (int)$qty;
            $product->save();
        } catch (\Throwable $th) {
            return null;
        }
    }
}
