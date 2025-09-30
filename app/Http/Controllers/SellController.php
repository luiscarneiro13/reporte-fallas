<?php

namespace App\Http\Controllers;

use App\Helpers\Operations;
use App\Http\Repositories\ProductRepository;
use App\Http\Repositories\ServiceRepository;
use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Models\MethodPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class SellController extends Controller
{

    public $articles, $services;

    public function __construct()
    {
        $this->articles = new ProductRepository();
        $this->services = new ServiceRepository();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $methodPayments = MethodPayment::select('id', 'name')->get();
        $branch_id = session('branch')->id;
        $customers = User::query()
            ->join('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', 'roles.id')
            ->join('user_branch', 'users.id', 'user_branch.user_id')
            ->where('user_branch.branch_id', $branch_id)
            ->where('roles.name', 'Cliente')
            ->select('users.*')
            ->orderBy('users.name', 'asc')
            ->take(30)
            ->get();

        $articles = $this->articles->getAllBranch($branch_id);
        $services = $this->services->getAllBranch($branch_id);

        return view('Sell.index', compact('customers', 'articles', 'methodPayments', 'services'));
    }

    public function forgetSession()
    {
        Session::put('customerSelected', "");
        Session::put('articlesSelected', "");
        Session::put('servicesSelected', "");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $insertSale = $this->converterInsertSale($request->all());
        $sale = Sale::create($insertSale);
        $sale = $this->insertDetails($sale, $request->all());
        $sale = Sale::find($sale->id);
        $this->selectMethodInvoice($sale, $request->all());
        $this->forgetSession();
        $sale = Sale::find($sale->id);
        try {
            return response()->json(['success' => true, 'data' => $sale]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    public function selectMethodInvoice($sale, $request)
    {
        switch ($request['invoiceMethod']) {
            case 'factura':
                $this->generateInvoice($sale, $request);
                break;

            case 'notaDespacho':
                $this->generateDeliveryNote($sale, $request);
                break;
        }
    }

    public function generateInvoice($sale, $request)
    {
        $inserts = $this->converterInsertInvoiceDeliveryNote($sale, $request);
        Invoice::create($inserts);
    }

    public function generateDeliveryNote($sale, $request)
    {
        $inserts = $this->converterInsertInvoiceDeliveryNote($sale, $request);
        DeliveryNote::create($inserts);
    }

    public function converterInsertInvoiceDeliveryNote($sale, $data)
    {
        info($data);
        $tax = $data['tax'] ?? 16;
        $subTotalInvoice = $data['total'];

        return [
            "branch_id" => $data["branch_id"],
            "sale_id" => $sale->id,
            "name"  => $data["invoice"]["name"], // Debe venir del request
            "rif" => $data["invoice"]["rif"], // Debe venir del request
            "address" => $data["invoice"]["address"], // Debe venir del request
            "rate" => $data['rate'], // Debe venir del request
            "average_rate" => $data['average_rate'] ?? $data['rate'], // Debe venir del request
            "sub_total" => $subTotalInvoice, // Debe venir del request
            "tax" => $tax ?? 0, // Debe venir del request
            "total" => $this->calculateTotal($subTotalInvoice, $tax), // Debe venir del request
        ];
    }

    public function calculateTotal($total, $tax)
    {
        return Operations::roundUp($total + ($total * ((float)$tax / 100)));
    }

    public function insertDetails($sale, $request)
    {
        if ($request['service'] == 1) {
            $inserts = $this->converterInsertSaleDetailsServices($request['selectedAllServices'], $request);
        } else {
            $inserts = $this->converterInsertSaleDetailsProducts($request['selectedAllArticles'], $request);
        }
        $sale->details()->createMany($inserts);
        return Sale::find($sale->id);
    }

    public function converterInsertSaleDetailsServices($data, $request)
    {
        return array_map(function ($item) use ($request) {
            $averageRate = $request['average_rate'];
            $priceBs = $item['price'] * $averageRate;
            return [
                'product_service_id' => $item['id'],
                'product' => $item['name'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'price_bs' => Operations::roundUp($priceBs),
                'rate' => $request['rate'],
                'average_rate' => $request['average_rate'],
                'sub_total' => $item['sub_total'],
                'sub_total_bs' => Operations::roundUp($priceBs * $item['qty']),
            ];
        }, $data);
    }

    public function converterInsertSaleDetailsProducts($data, $request)
    {
        return array_map(function ($item) use ($request) {
            $this->discountProduct($item['id'], $item['qty']);
            $averageRate = $request['average_rate'];
            $priceBs = $item['price'] * $averageRate;
            return [
                'product_service_id' => $item['id'],
                'product' => $item['name'] . ' - ' . $item["description"] . ' - ' . $item["brand"] . ' - ' . $item["type"],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'price_bs' => Operations::roundUp($priceBs),
                'rate' => $request['rate'],
                'average_rate' => $request['average_rate'] ?? $request['rate'],
                'sub_total' => $item['sub_total'],
                'sub_total_bs' => Operations::roundUp($priceBs * $item['qty']),
            ];
        }, $data);
    }

    public function discountProduct($id, $qty)
    {
        $product = Product::find($id);
        $product->available_qty = $product->available_qty - $qty;
        $product->save();
    }

    public function converterInsertSale($data)
    {
        $inserts = [
            "customer_id" => $data["customer_id"],
            "branch_id" => $data["branch_id"],
            "rate" => $data["rate"],
            "average_rate" => $data["average_rate"] ?? $data["rate"],
            "total_items" => $data["totalItems"],
            "total" => $data["total"],
            "total_bs" => Operations::roundUp($data["total_bs"]),
            "service" => $data["service"],
        ];

        if (isset($data["method_payment_id"])) {
            $inserts['method_payment_id'] = $data["method_payment_id"];
        }

        return $inserts;
    }
}
