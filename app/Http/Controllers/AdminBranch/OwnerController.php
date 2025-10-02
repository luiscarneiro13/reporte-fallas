<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\OwnerEditRequest;
use App\Http\Requests\OwnerRequest;
use App\Models\Owner;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.owners.index";

    public function index()
    {
        $query = request('query');
        $owners = Owner::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery
                        ->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                    ;
                });
            })
            ->orderBy('last_name', 'asc')
            ->paginate(10);
        return view('AdminBranch.Owners.index', compact('owners'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('AdminBranch.Owners.create', compact('back_url'));
    }

    public function store(OwnerRequest $request)
    {
        try {
            $owner = new Owner();
            $owner->first_name = $request->input('first_name');
            $owner->last_name = $request->input('last_name');
            $owner->save();

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $owner]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Propietario guardado: ' . $owner->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $owner = Owner::find($id);
        return view('AdminBranch.Owners.edit', compact('owner', 'back_url'));
    }

    public function update(OwnerEditRequest $request, string $id)
    {
        try {
            $owner = Owner::find($id);
            $owner->first_name = $request->input('first_name');
            $owner->last_name = $request->input('last_name');
            $owner->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Propietario actualizado: ' . $owner->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $owner = Owner::find($id);
            $owner->delete();
            return $this->alertSuccess(self::INDEX, 'Propietario eliminado: ' . $owner->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
