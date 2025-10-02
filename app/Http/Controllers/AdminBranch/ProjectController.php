<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectEditRequest;
use App\Http\Requests\ProjectRequest;
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
        return view('AdminBranch.Projects.index', compact('projects'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        return view('AdminBranch.Projects.create', compact('back_url'));
    }

    public function store(ProjectRequest $request)
    {
        try {
            $project = new Project();
            $project->name = $request->input('name');
            $project->description = $request->input('description');
            $project->branch_id = session('branch')->id;
            $project->save();

            if ($request->ajax()) {
                // Si es AJAX, devuelve un JSON
                return response()->json(['data' => $project]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Proyecto creado: ' . $project->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function edit(string $id)
    {
        $back_url = request()->back_url ?? null;
        $project = Project::find($id);
        return view('AdminBranch.Projects.edit', compact('project', 'back_url'));
    }

    public function update(ProjectEditRequest $request, string $id)
    {
        try {
            $project = Project::find($id);
            $project->name = $request->input('name');
            $project->description = $request->input('description');
            $project->save();

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            return $this->alertSuccess(self::INDEX, 'Proyecto actualizado: ' . $project->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }

    public function destroy(string $id)
    {
        try {
            $project = Project::find($id);
            $project->delete();
            return $this->alertSuccess(self::INDEX, 'Proyecto eliminado: ' . $project->name);
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}
