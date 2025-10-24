<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProjectEditRequest;
use App\Http\Requests\V1\ProjectRequest;
use App\Models\Customer;
use App\Models\Division;
use App\Models\Project;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ProjectController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.projects.index";

    public function index()
    {
        $query = request('query');
        $projects = Project::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('projects.name', 'like', "%{$query}%")
                        ->orWhere('customers.name', 'like', "%{$query}%")
                        ->orWhere('divisions.name', 'like', "%{$query}%")
                        ->orWhere('projects.geographic_area', 'like', "%{$query}%")
                        ->orWhere('projects.contract_number', 'like', "%{$query}%");
                });
            })
            ->join('customers', 'customers.id', '=', 'projects.customer_id')
            ->join('divisions', 'divisions.id', '=', 'projects.division_id')
            ->select(
                'projects.id',
                'customers.name as customer_name',
                'projects.name as project_name',
                'divisions.name as division_name',
                'projects.geographic_area as project_geographic_area',
                'projects.contract_number as project_contract_number',
            )
            ->orderBy('projects.updated_at', 'desc')
            ->paginate(10);

        return view('V1.AdminBranch.Projects.index', compact('projects'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        $customers = Customer::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $divisions = Division::where('branch_id', session('branch')->id)->pluck('name', 'id');
        return view('V1.AdminBranch.Projects.create', compact('back_url', 'customers', 'divisions'));
    }

    public function store(ProjectRequest $request)
    {
        try {
            $item = new Project();
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->customer_id = $request->input('customer_id');
            $item->division_id = $request->input('division_id');
            $item->geographic_area = $request->input('geographic_area');
            $item->contract_number = $request->input('contract_number');
            $item->branch_id = session('branch')->id;
            $item->save();

            // Elimina la instancia 'fault_data' del contenedor. Esto es para que se recarguen los selects globales
            // Se creará nuevamente en la próxima solicitud y estará disponible en toda la app
            App::forgetInstance('fault_data');

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Proyecto creado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $project = Project::find($id);
        $customers = Customer::where('branch_id', session('branch')->id)->pluck('name', 'id');
        $divisions = Division::where('branch_id', session('branch')->id)->pluck('name', 'id');
        return view('V1.AdminBranch.Projects.edit', compact('project', 'back_url', 'customers', 'divisions'));
    }

    public function update(ProjectEditRequest $request, string $id)
    {
        try {
            $item = Project::find($id);
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->customer_id = $request->input('customer_id');
            $item->division_id = $request->input('division_id');
            $item->geographic_area = $request->input('geographic_area');
            $item->contract_number = $request->input('contract_number');
            $item->save();

            // Elimina la instancia 'fault_data' del contenedor. Esto es para que se recarguen los selects globales
            // Se creará nuevamente en la próxima solicitud y estará disponible en toda la app
            App::forgetInstance('fault_data');

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Proyecto actualizado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $item = Project::find($id);
            $item->delete();
            return $this->alertSuccess(self::INDEX, 'Proyecto eliminado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
