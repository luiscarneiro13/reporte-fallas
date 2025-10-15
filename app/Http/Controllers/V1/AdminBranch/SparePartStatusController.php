<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SparePartStatusEditRequest;
use App\Http\Requests\V1\SparePartStatusRequest;
use App\Models\SparePartStatus;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class SparePartStatusController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.spare.part.statuses.index";

    public function index()
    {
        $query = request('query');
        $sparePartStatuses = SparePartStatus::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('V1.AdminBranch.SparePartStatuses.index', compact('sparePartStatuses'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('V1.AdminBranch.SparePartStatuses.create', compact('back_url'));
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $sparePartStatus = SparePartStatus::find($id);
        return view('V1.AdminBranch.SparePartStatuses.edit', compact('sparePartStatus', 'back_url'));
    }

    public function store(SparePartStatusRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(SparePartStatusEditRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(Request $request, string $id = null)
    {
        try {

            $item = $id ? SparePartStatus::find($id) : new SparePartStatus();

            $item->name = $request->input('name');
            $item->branch_id = session('branch')->id;
            $item->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            $action_msg = $id ? 'actualizado' : 'creado';
            $message = "Status de repuesto {$action_msg}: " . $item->name;
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            // Manejo de errores
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage()); // Se recomienda logear el error
            return $this->alertError(self::INDEX, "Error al {$action} el status de repuestos: " . $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = SparePartStatus::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Estatus de repuesto eliminado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
