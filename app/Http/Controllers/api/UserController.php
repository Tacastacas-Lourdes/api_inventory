<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\User\UpdateRoleAdminRequest;
use App\Models\User;
use App\Models\UserApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:user_approval', ['only' => ['registerAccount']]);
        $this->middleware('permission:user-user_assign_role', ['only' => ['changeRole']]);
//        $this->middleware('permission:user_disapprove', ['only' => ['disapprove']]);
    }

    /**
     * Register approved accounts api
     *
     * @param  UserApproval  $requests
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
            'email' => $requests->email,
            'password' => $requests->password,
            'role_request' => $requests->role_request, ])->assignRole('admin');
        $requests->delete();
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['attributes'] = $user;

        return $this->sendResponse($success, 'Approved account register successfully.');
    }

    public function disapprove(UserApproval $requests): JsonResponse
    {
        $requests->delete();

        return $this->sendResponse([], 'Admin disapproved user account.');
    }

    public function changeRole(UpdateRoleAdminRequest $request, User $user): JsonResponse
    {
        $input = $request->validated();
        DB::table('model_has_roles')->where('model_id', '=', $user->id)->delete();
        $user->assignRole($input->role_id);
        $success['attributes'] = $user;

        return $this->sendResponse($success, 'Admin change user role successfully.');
    }

    public function approvalList(): JsonResponse
    {
        $users_approval = UserApproval::all();
        if (is_null($users_approval)) {
            return $this->sendError('No Record.');
        }

        return $this->sendResponse($users_approval, 'Accounts for approval retrieved successfully.');
    }

    public function adminList(): JsonResponse
    {
        $admins = User::all();
        if (is_null($admins)) {
            return $this->sendError('No Record.');
        }

        return $this->sendResponse($admins, 'Admin accounts retrieved successfully.');
    }

    public function getAdminById(User $user): JsonResponse
    {
        return $this->sendResponse($user, 'Admin details retrieved successfully.');
    }
}
