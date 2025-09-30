<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Models\MethodPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePaymentMixed;
use App\Traits\AlertResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesHistoryController extends Controller
{
    use AlertResponser;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->input('query');
        // Separar las fechas
        if ($query) {
            $dates = explode(' - ', $query);
            // Asegurarse de que se incluya todo el día y que se usa el formato en que viene la info 2025/01/01
            // Carbon se encarga de arreglar el formato para consultar a la base de datos
            $startDate = Carbon::createFromFormat('Y/m/d', $dates[0])->startOfDay();
            $endDate = Carbon::createFromFormat('Y/m/d', $dates[1])->endOfDay();
        } else {
            // $today = Carbon::today()->format('Y/m/d');
            // $dateRange = $today . ' - ' . $today;
            // $encodedDateRange = str_replace(' ', '%20', $dateRange);
            // $encodedDateRange = str_replace('/', '%2F', $dateRange);
            // $url = route('admin.sucursal.sales.history.index') . '?query=' . $encodedDateRange;
            // return redirect($url);
        }

        $salesQuery = Sale::where('branch_id', session('branch')->id)->orderBy('id', 'desc');

        if (isset($startDate) && isset($endDate)) {
            $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $sales = $salesQuery->paginate(10);

        if (request()->back_url) {
            return redirect(request()->back_url);
        }

        return view('AdminBranch.SalesHistory.index', compact('sales'));

        // *************************************** NO BORRAR NO BORRAR, SIRVE DE EJEMPLO
        // if ($query) {
        //     $salesQuery->where(function ($q) use ($query) {
        //         $q->whereHas('customer', function ($q) use ($query) {
        //             $q->where('name', 'like', '%' . $query . '%');
        //         })->orWhereHas('methodPayment', function ($q) use ($query) {
        //             $q->where('name', 'like', '%' . $query . '%');
        //         });
        //     });
        //     $salesQuery->orWhere('created_at', 'like', $query);
        // }
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

        $paymentMixed = $request->input('paymentMixed');

        if ($paymentMixed) {
            $data = ["sale_id" => $id];

            if (isset($paymentMixed['bolivares_efectivo'])) {
                $data['bolivares_efectivo'] = $paymentMixed['bolivares_efectivo'];
            }

            if (isset($paymentMixed['dolares_efectivo'])) {
                $data['dolares_efectivo'] = $paymentMixed['dolares_efectivo'];
            }

            if (isset($paymentMixed['pago_movil'])) {
                $data['pago_movil'] = $paymentMixed['pago_movil'];
            }

            if (isset($paymentMixed['biopago'])) {
                $data['biopago'] = $paymentMixed['biopago'];
            }

            if (isset($paymentMixed['punto_venta_venezuela'])) {
                $data['punto_venta_venezuela'] = $paymentMixed['punto_venta_venezuela'];
            }

            if (isset($paymentMixed['punto_venta_banesco'])) {
                $data['punto_venta_banesco'] = $paymentMixed['punto_venta_banesco'];
            }

            if (isset($paymentMixed['zelle'])) {
                $data['punto_venta_banesco'] = $paymentMixed['punto_venta_banesco'];
            }

            SalePaymentMixed::create($data);
        }

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
