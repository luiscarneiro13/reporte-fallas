<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProjectEditRequest;
use App\Http\Requests\V1\ProjectRequest;
use App\Models\Project;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

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
                    $subQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('V1.AdminBranch.Projects.index', compact('projects'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('V1.AdminBranch.Projects.create', compact('back_url'));
    }

    public function store(ProjectRequest $request)
    {
        try {
            $item = new Project();
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

            return $this->alertSuccess(self::INDEX, 'Proyecto creado: ' . $item->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $project = Project::find($id);
        return view('V1.AdminBranch.Projects.edit', compact('project', 'back_url'));
    }

    public function update(ProjectEditRequest $request, string $id)
    {
        try {
            $item = Project::find($id);
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->save();

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
