<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterAdminRequest;
use App\Models\User;
use App\Models\UserApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @param  RegisterAdminRequest  $request
     * @return JsonResponse
     */
    public function register(RegisterAdminRequest $request): JsonResponse
    {
        $input = $request->validated();
        $input['password'] = bcrypt($input['password']);
        if (User::exists()) {
            $user = UserApproval::create($input);
            $success['attributes'] = $user;
            $success['role'] = Role::all();

            return $this->sendResponse($success, 'User Account successfully queue for admin Approval.');
        } else {
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
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $input = $request->validated();
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['attributes'] = $user;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return $this->sendResponse($user, 'User logged out successfully.');
    }
}
