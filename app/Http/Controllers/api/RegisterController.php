<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterAdminRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
        if (User::query()->exists()) {
            $user = User::query()->create($input)->assignRole('guest');
            if (is_null($input['company_id'])) {
                return $this->sendResponse(new UserResource($user), 'User Account successfully queue for admin Approval.');
            }
            $user->company()->sync($input['company_id']);
        } else {
            $user = User::query()->create($input)->assignRole('super_admin');
            $user->createToken('MyApp')->plainTextToken;
        }

        return $this->sendResponse(new UserResource($user), 'User register successfully.');
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
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password'], 'deactivated_at' => null])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['attributes'] = $user;

            return $this->sendResponse($success, 'User login successfully.');
        } elseif (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            return $this->sendError('Your account has been deactivated. Please contact the administrator to have your account activated.', ['error' => 'Unauthorised']);
        } else {
            return $this->sendError('Wrong credentials.', ['error' => 'Unauthorised']);
        }
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();
        //dd($user->token()->delete);
        return $this->sendResponse($user, 'User logged out successfully.');
    }
}
