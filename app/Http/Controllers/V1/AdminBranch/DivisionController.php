<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.divisions.index";

    public function index()
    {
        $query = request('query');
        $divisions = Division::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('V1.AdminBranch.Divisions.index', compact('divisions'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('V1.AdminBranch.Divisions.create', compact('back_url'));
    }

    public function store(Request $request)
    {
        try {
            $item = new Division();
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->branch_id = session('branch')->id;
            $item->save();

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'División creada: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $division = Division::find($id);
        return view('V1.AdminBranch.Divisions.edit', compact('division', 'back_url'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $item = Division::find($id);
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'División actualizada: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = Division::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'División eliminada: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
