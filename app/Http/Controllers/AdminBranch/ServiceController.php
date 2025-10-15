<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.services.index";

    public function index()
    {
        $query = request('query');
        $services = Service::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%")
                        ->orWhere('price', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('AdminBranch.Services.index', compact('services'));
    }

    public function create()
    {
        return view('AdminBranch.Services.create');
    }

    public function store(ServiceRequest $request)
    {
        try {
            $service = new Service();
            $service->name = $request->input('name');
            $service->price = $request->input('price');
            $service->branch_id = session('branch')->id;
            $service->save();

            return $this->alertSuccess(self::INDEX, 'Servicio guardado: ' . $service->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $service = Service::find($id);
        return view('AdminBranch.Services.edit', compact('service'));
    }

    public function update(ServiceRequest $request, string $id)
    {
        try {
            $service = Service::find($id);
            $service->name = $request->input('name');
            $service->price = $request->input('price');
            $service->save();

            return $this->alertSuccess(self::INDEX, 'Servicio actualizado: ' . $service->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $service = Service::find($id);
            $service->delete();
            return $this->alertSuccess(self::INDEX, 'Servicio eliminado: ' . $service->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
