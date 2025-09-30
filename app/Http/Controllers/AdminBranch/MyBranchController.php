<?php

namespace App\Http\Controllers\AdminBranch;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MyBranchController extends Controller
{
    public function edit()
    {
        $branch = Branch::find(session('branch')->id);
        return view('AdminBranch.Branch.edit', compact('branch'));
    }

    public function update(BranchRequest $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'rif' => 'required|string|min:3',
            'address' => 'string|min:3',
            'description' => 'string|min:3',
            'phone' => 'numeric|min:3',
            'email' => 'email|unique:users,email|min:3|',
            // 'logo' => 'required',
        ]);

        $file = $request->file('logo');

        try {

            $branch = Branch::find($id);

            $branch->name = $request->input('name');
            $branch->description = $request->input('description');
            $branch->phone = $request->input('phone');
            $branch->email = $request->input('email');
            $branch->rif = $request->input('rif') ?? '';
            $branch->address = $request->input('address') ?? '';

            if ($file) {
                $images = new Images();
                $url = $images->uploadImage($request->file('logo'), 'logos');
                $branch->logo = $url;
            }

            $branch->save();

            Session::put('branch', $branch);

            return redirect()->route('admin.branch.my-branch')->with(['state' => 'success', 'message' => 'Se actualizó la sucursal ' . $branch->name]);
        } catch (\Throwable $th) {
            return $this->alertError('admin.branch.my-branch');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
