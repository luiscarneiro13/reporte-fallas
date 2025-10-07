<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\TypeArticleRequest;
use App\Models\TypeArticle;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class TypeArticleController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.type.articles.index";

    public function index()
    {
        $query = request('query');
        $types = TypeArticle::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('AdminBranch.TypeArticle.index', compact('types'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('AdminBranch.TypeArticle.create', compact('back_url'));
    }

    public function store(TypeArticleRequest $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
        ]);

        try {
            $typeArticle = new TypeArticle();
            $typeArticle->name = $request->input('name');
            $typeArticle->branch_id = session('branch')->id;
            $typeArticle->save();

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $typeArticle]);
            }


            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Tipo de artículo guardado: ' . $typeArticle->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $typeArticle = TypeArticle::find($id);
        return view('AdminBranch.TypeArticle.edit', compact('typeArticle', 'back_url'));
    }

    public function update(TypeArticleRequest $request, string $id)
    {
        try {
            $typeArticle = TypeArticle::find($id);
            $typeArticle->name = $request->input('name');
            $typeArticle->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Tipo de artículo actualizado: ' . $typeArticle->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $typeArticle = TypeArticle::find($id);
            $typeArticle->delete();
            return $this->alertSuccess(self::INDEX, 'Marca eliminada: ' . $typeArticle->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
