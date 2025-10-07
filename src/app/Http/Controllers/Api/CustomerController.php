<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerApiRequest;
use App\Models\User;
use App\Models\UserBranch;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class CustomerController extends Controller
{
    use ApiResponser;

    public function index(Request $request)
    {
        $query = $request->input('query');
        $data = User::query()
            ->leftJoin('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', 'roles.id')
            ->leftJoin('user_branch', 'users.id', 'user_branch.user_id')
            ->leftJoin('branches', 'user_branch.branch_id', 'branches.id')
            ->select('users.*', 'roles.name as rol', 'branches.id as branch_id', 'branches.name as branch')
            ->where('roles.name', 'Cliente')
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('users.cedula', 'LIKE', '%' . (string)$query . '%')
                    ->orWhere('users.name', 'LIKE', '%' . (string)$query . '%');
            })
            ->take(30)
            ->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(CustomerApiRequest $request)
    {
        try {

            $branch_id = $request->input('branch_id');
            $email = $request->input('email') ?? null;
            $phone = $request->input('phone') ?? null;
            $cedula = $request->input('cedula') ?? null;
            $address = $request->input('address') ?? null;

            $customer = new User();
            $customer->name = $request->input('name');
            $customer->cedula = $cedula;
            $customer->address = $address;
            $customer->password = uniqid();

            if ($email) {
                $customer->email = $email;
            } else {
                $customer->email = uniqid() . '@sinemail.com';
            }

            if ($phone) {
                $customer->phone = $phone;
            }

            $customer->email_verified_at = now();
            $customer->save();
            $customerSaved = $customer;

            $rol = Role::where('name', "Cliente")->first();
            $customer->roles()->sync([$rol->id]);

            $customer->branches()->sync([$branch_id]);

            return response()->json(['success' => true, 'data' => $customerSaved]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'data' => $th->getMessage()]);
        }
    }
}
