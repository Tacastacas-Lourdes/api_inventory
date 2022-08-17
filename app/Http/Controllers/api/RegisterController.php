<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\UserApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use Spatie\Permission\Models\Role;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
            $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'required',
            'suffix' => 'required',
            'role_request' => '',//relation nga validation
            'email' => 'required|email',
            'password' => 'required|same:confirmPassword',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
//        $r= DB::table('model_has_roles')
//            ->where('model_id','=',userId)
//            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
//            ->select('roles.name')
//            ->first();// kuhaon ang role name
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        if (User::exists()) {
            $user = UserApproval::create($input);
            $success['attributes'] = $user;
            $success['role'] = Role::all();
            return $this->sendResponse($success, 'User Account successfully queue for admin Approval.');
        }else{
            $user = User::create($input)->assignRole('super_admin');
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['attributes'] = $user;
            $success['role'] = Role::all();
        }
        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['attributes'] =  $user;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return $this->sendResponse($user, 'User logout successfully.');
    }


}
