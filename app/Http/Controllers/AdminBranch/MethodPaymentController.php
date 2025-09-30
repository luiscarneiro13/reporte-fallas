<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\MethodPaymentRequest;
use App\Models\MethodPayment;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class MethodPaymentController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.method.payment.index";

    public function index()
    {
        $query = request('query');
        $methodPayments = MethodPayment::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('AdminBranch.MethodPayment.index', compact('methodPayments'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('AdminBranch.MethodPayment.create', compact('back_url'));
    }

    public function store(MethodPaymentRequest $request)
    {
        try {
            $methodPayment = new MethodPayment();
            $methodPayment->name = $request->input('name');
            $methodPayment->currency = $request->input('currency');
            $methodPayment->branch_id = session('branch')->id;
            $methodPayment->save();

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $methodPayment]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Metodo de pago guardado: ' . $methodPayment->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $methodPayment = MethodPayment::find($id);
        return view('AdminBranch.MethodPayment.edit', compact('methodPayment', 'back_url'));
    }

    public function update(MethodPaymentRequest $request, string $id)
    {
        try {
            $methodPayment = MethodPayment::find($id);
            $methodPayment->name = $request->input('name');
            $methodPayment->currency = $request->input('currency');
            $methodPayment->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Método de pago actualiazdo: ' . $methodPayment->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $methodPayment = MethodPayment::find($id);
            $methodPayment->delete();
            return $this->alertSuccess(self::INDEX, 'Método de pago eliminado: ' . $methodPayment->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
