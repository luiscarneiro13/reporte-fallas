<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use AlertResponser;

    public function index()
    {
        $branches = Branch::all();
        return view('SuperAdmin.Branches.index', compact('branches'));
    }

    public function create()
    {
        return view('SuperAdmin.Branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'description' => 'string|min:3',
            'phone' => 'numeric|min:3',
            'email' => 'email|unique:customers,email|min:3|',
        ]);

        try {
            $item = new Branch();

            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->phone = $request->input('phone');
            $item->email = $request->input('email');

            $item->save();

            return redirect()->route('branches.index', $item)->with(['state' => 'success', 'message' => 'Se creó la Sucursal ' . $item->name]);
        } catch (\Throwable $th) {
            return $this->alertError('branches.index');
        }
    }

    public function edit(string $id)
    {
        $branch = Branch::find($id);
        return view('SuperAdmin.Branches.edit', compact('branch'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'description' => 'string|min:3',
            'phone' => 'numeric|min:3',
            'email' => 'email|unique:customers,email|min:3|',
        ]);

        try {

            $branch = Branch::find($id);

            $branch->name = $request->input('name');
            $branch->description = $request->input('description');
            $branch->phone = $request->input('phone');
            $branch->email = $request->input('email');

            $branch->save();

            return redirect()->route('branches.index', $branch)->with(['state' => 'success', 'message' => 'Se actualizó la sucursal ' . $branch->name]);
        } catch (\Throwable $th) {
            return $this->alertError('branches.index');
        }
    }

    public function destroy(string $id)
    {
        $branch = Branch::find($id);
        $branch->delete();
        return back();
    }
}
