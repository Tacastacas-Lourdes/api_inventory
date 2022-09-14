<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterAdminRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 */
class RegisterController extends BaseController
{
    /**
     * Register
     *
     * @bodyParam employee_id string required Employee ID of the user. Example: 2019-001
     * @bodyParam first_name string required First name of the user. Example: Oliver
     * @bodyParam last_name string required Last name of the user. Example: Pierce
     * @bodyParam middle_name string Middle name of the user. Example: Mason
     * @bodyParam suffix string required Suffix name of the user. Example: Jr.
     * @bodyParam company_id int Company ID for the employees. Example: 1
     * @bodyParam role_request string Role request for the admins. Example: admin
     * @bodyParam email string required email of the user. Example: oliver.pierce@gmail.com
     * @bodyParam password string required The value and confirmPassword must match. Example: cmp5ZVpIMe
     * @bodyParam confirmPassword string required confirm password. Example: cmp5ZVpIMe
     *
     * @apiResourceCollection App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     *
     * @param  RegisterAdminRequest  $request
     * @return JsonResponse
     */
    public function register(RegisterAdminRequest $request): JsonResponse
    {
        $input = $request->validated();
        $input['password'] = bcrypt($input['password']);
        if (User::query()->exists()) {
            $user = User::query()->create($input);
            if (is_null($input['company_id'])) {
                return $this->sendResponse(new UserResource($user), 'User Account successfully queue for admin Approval.');
            }
            $user->company()->sync($input['company_id']);

            return $this->sendResponse(new UserResource($user), 'User Account successfully queue for admin Approval.');
        } else {
//            $user->role_request = 'Super Administrator';
            $user = User::query()->create($input)->assignRole('super_admin');
            $user->approved_at = now();
            $user->save();

            return $this->sendResponse(new UserResource($user), 'Super administrator register successfully.');
        }
    }

    /**
     * Login
     *
     * @bodyParam email string required Email of the user. Example: oliver.pierce@gmail.com
     * @bodyParam  password required Password of the user. Example: cmp5ZVpIMe
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $input = $request->validated();
        $user = User::query()->where('email', $input['email'])->first();
        if (! $user || ! Hash::check($input['password'], $user->password)) {
            return $this->sendError('Wrong credentials.', ['error' => 'Unauthorised']);
        } elseif ($user->isDeactivated()) {
            return $this->sendError(
                'Your account has been deactivated. Please contact the system administrator.',
                ['error' => 'Unauthorised']
            );
        } elseif ($user->isApproved(false)) {
            return $this->sendError(
                'Your account is not yet approved. Please contact the system administrator.',
                ['error' => 'Unauthorised']
            );
        } elseif ($user->isDisapproved()) {
            return $this->sendError(
                'Your account was disapproved. Please contact the system administrator.',
                ['error' => 'Unauthorised']
            );
        } else {
            return $this->sendResponse(new UserResource($user), 'User logged in successfully.');
        }
    }

    /**
     * Logout
     *
     * @header Authorization Bearer {token}
     * @authenticated
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return $this->sendResponse($user, 'User logged out successfully.');
    }
}
