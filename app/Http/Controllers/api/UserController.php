<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\UserApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:user_approval', ['only' => ['registerAccount']]);
//        $this->middleware('permission:product-create', ['only' => ['create','store']]);
//        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
//        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    /**
     * Register approved accounts api
     *
     * @param UserApproval $requests
     * @return JsonResponse
     */
    public function registerAccount(UserApproval $requests): JsonResponse
    {
        $user = User::create([
            'employee_id' => $requests->employee_id,
            'last_name' => $requests->last_name,
            'first_name' => $requests->first_name,
            'middle_name' => $requests->middle_name,
            'suffix' => $requests->suffix,
            'email' =>$requests->email,
            'password' =>$requests->password,
            'role_request'=> $requests->role_request])->assignRole('admin');
        $requests->delete();
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['attributes'] = $user;
        return $this->sendResponse($success, 'Approved account register successfully.');

    }
    public function disapprove(UserApproval $requests): JsonResponse
    {
        $requests->delete();
        return $this->sendResponse([], 'Admin disapprove user account.');
    }

    public function changeRole(Request $request, User $user): JsonResponse
    {
        $validator  = Validator::make($request->all(), [
            'role_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        DB::table('model_has_roles')->where('model_id', '=', $user->id)->delete();
        $user->assignRole($request->role_id);
//        $k = DB::table('model_has_roles')
//            ->where('model_id','=',$user->id)
//            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
//            ->select('roles.name')
//            ->first();
//        $user->removeRole($k);
        $success['attributes'] = $user;
        return $this->sendResponse($success, 'Admin change user role successfully.');
    }
}
