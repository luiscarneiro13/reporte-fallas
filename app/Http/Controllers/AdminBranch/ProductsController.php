<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\Rules\DecimalRule;
use App\Models\Brand;
use App\Models\ModelVehicle;
use App\Models\Product;
use App\Models\TypeArticle;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

    use AlertResponser;

    const INDEX = "admin.sucursal.products.index";

    public function index()
    {
        $query = request('query');
        $products = Product::query()
            ->leftJoin('brands', 'products.brand_id', 'brands.id')
            ->leftJoin('type_articles', 'products.type_article_id', 'type_articles.id')
            ->where('products.branch_id', session('branch')->id)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('products.name', 'like', "%{$query}%")
                        ->orWhere('products.description', 'like', "%{$query}%")
                        ->orWhere('products.available_qty', 'like', "%{$query}%")
                        ->orWhere('products.price', 'like', "%{$query}%")
                        ->orWhere('brands.name', 'like', "%{$query}%")
                        ->orWhere('type_articles.name', 'like', "%{$query}%");
                });
            })
            ->select('products.*', 'brands.name as brand', 'type_articles.name as type_article')
            ->orderBy('products.name', 'asc')
            ->paginate(10);

        return view('AdminBranch.Products.index', compact('products'));
    }

    public function create()
    {
        $branch_id = session('branch')->id;
        $brands = Brand::get()->mapWithKeys(function ($brand) {
            return [$brand->id => $brand->name];
        });
        $modelVehicles = ModelVehicle::get()->mapWithKeys(function ($item) {
            return [$item->id => $item->name];
        });
        $typeArticles = TypeArticle::get()->mapWithKeys(function ($type) {
            return [$type->id => $type->name];
        });
        return view('AdminBranch.Products.create', compact('brands', 'typeArticles', 'branch_id', 'modelVehicles'));
    }

    public function store(ProductRequest $request)
    {
        try {
            $product = new Product();
            $product->branch_id = session('branch')->id;
            $product->name = $request->input('name');
            $product->description = $request->input('description');
            // $product->available_qty = $request->input('available_qty');
            // $product->price = $request->input('price');
            $product->brand_id = $request->input('brand_id');
            $product->type_article_id = $request->input('type_article_id');
            $product->save();

            if (request()->modelVehicles) {
                $product->modelVehicles()->attach(request()->modelVehicles);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Producto guardado: ' . $product->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $brands = Brand::get()->mapWithKeys(function ($brand) {
            return [$brand->id => $brand->name];
        });
        $modelVehicles = ModelVehicle::get()->mapWithKeys(function ($item) {
            return [$item->id => $item->name];
        });
        $typeArticles = TypeArticle::get()->mapWithKeys(function ($type) {
            return [$type->id => $type->name];
        });
        $product = Product::with(['modelVehicles' => function ($q) {
            $q->select('model_vehicles.id');
        }])->find($id);

        return view('AdminBranch.Products.edit', compact('brands', 'typeArticles', 'product', 'modelVehicles'));
    }

    public function update(ProductRequest $request, string $id)
    {
        try {
            $product = Product::find($id);
            $product->branch_id = session('branch')->id;
            $product->name = $request->input('name');
            $product->description = $request->input('description');
            $product->available_qty = $request->input('available_qty');
            $product->price = $request->input('price');
            $product->brand_id = $request->input('brand_id');
            $product->type_article_id = $request->input('type_article_id');
            $product->save();

            if (request()->modelVehicles) {
                $product->modelVehicles()->sync(request()->modelVehicles);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Producto actualizado: ' . $product->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $product = Product::find($id);
            $product->modelVehicles()->detach();
            $product->productEntry()->delete();
            $product->saleDetails()->delete();
            $product->delete();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Producto eliminado: ' . $product->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
