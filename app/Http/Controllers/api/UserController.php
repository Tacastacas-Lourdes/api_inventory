<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateRoleAdminRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\ApprovalResource;
use App\Http\Resources\EmployeeResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * @group User Management
 */
class UserController extends BaseController
{
    public function __construct()
    {
//        $this->middleware('permission:user_approval', ['only' => ['registerAccount']]);
//        $this->middleware('permission:user-user_assign_role', ['only' => ['changeRole']]);
//        $this->middleware('permission:user_disapprove', ['only' => ['disapprove']]);
    }

    public function checkUsersRecord(): JsonResponse
    {
        $user = User::all();
        if ($user->isNotEmpty()) {
            return $this->sendResponse(['empty' => 'false'], 'The user records are not empty.');
        }

        return $this->sendError('No record found.', ['empty' => 'true']);
    }

    /**
     * Register approved accounts api
     *
     * @param  User  $requestor
     * @return JsonResponse
     */
    public function approveAccount(User $requestor): JsonResponse
    {
        $requestor->approve();

        return $this->sendResponse($requestor, 'Approved account register successfully.');
    }

    /**
     * Register disapproved accounts api
     *
     * @param  User  $requestor
     * @return JsonResponse
     */
    public function disapproveAccount(User $requestor): JsonResponse
    {
        $requestor->disapprove();

        return $this->sendResponse($requestor, 'Admin disapproved user account.');
    }

    /**
     * User Change Role
     *
     * @param  UpdateRoleAdminRequest  $request
     * @param  User  $user
     * @return JsonResponse
     */
    public function changeRole(UpdateRoleAdminRequest $request, User $user): JsonResponse
    {
        $input = $request->validated();
        $user->roles()->sync($input['role_id']);
        $user->load('roles');

        return $this->sendResponse($user, 'Admin change user role successfully.');
    }

    /**
     * Unapproved accounts List
     *
     * @return JsonResponse
     */
    public function approvalList(): JsonResponse
    {
        $users_approval = User::unapproved()->get();
        if ($users_approval->isNotEmpty()) {
            return $this->sendResponse(ApprovalResource::collection($users_approval), 'Accounts for approval retrieved successfully.');
        }

        return $this->sendError('No Record.');
    }

    /**
     * Disapproved accounts List
     *
     * @return JsonResponse
     */
    public function disapprovedList(): JsonResponse
    {
        $user_disapproved = User::disapproved()->get();
//        dd($user_disapproved);
        if ($user_disapproved->isNotEmpty()) {
            return $this->sendResponse(ApprovalResource::collection($user_disapproved), 'Disapproved accounts retrieved successfully.');
        }

        return $this->sendError('No Record.');
    }

    /**
     * Activate user account
     *
     * @param  User  $user
     * @return JsonResponse
     */
    public function activate(User $user): JsonResponse
    {
        $user->activate();

        return $this->sendResponse($user, 'User account has been activated.');
    }

    /**
     * Deactivate user accounts
     *
     * @param  User  $user
     * @return JsonResponse
     */
    public function deactivate(User $user): JsonResponse
    {
        $user->deactivate();

        return $this->sendResponse($user, 'Deactivated user account.');
    }

    /**
     * Deactivated Accounts List
     *
     * @return JsonResponse
     */
    public function deactivatedAccount(): JsonResponse
    {
        $user = User::deactivated()->get();
        if ($user->isNotEmpty()) {
            return $this->sendResponse($user, 'Deactivated user accounts were retrieved successfully.');
        }

        return $this->sendError('No Record.');
    }

    /**
     * Admin Account List
     *
     * @return JsonResponse
     */
    public function adminList(): JsonResponse
    {
        $admins = User::query()->where('role_request', '!=', null)->get();
//        $admins = User::query()->whereHas('roles', function($q){
//            $q->where('name', 'like', '%admin%');})->get();
        if ($admins->isNotEmpty()) {
            return $this->sendResponse(AdminResource::collection($admins), 'Admin accounts retrieved successfully.');
        }

        return $this->sendError('No Record.');
    }

    /**
     * Employee Account List
     *
     * @return JsonResponse
     */
    public function employeeList(): JsonResponse
    {
        $employees = User::query()->role('employee')->get();
        if ($employees->isNotEmpty()) {
            return $this->sendResponse(EmployeeResource::collection($employees), 'Employees accounts retrieved successfully');
        }

        return $this->sendError('No record.');
    }

    /**
     * @param  User  $user
     * @return JsonResponse
     */
    public function getUserById(User $user): JsonResponse
    {
        return $this->sendResponse($user, 'Admin details retrieved successfully.');
    }

    /**
     * @param  UpdateProfileRequest  $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request, User $user): JsonResponse
    {
//        $user = $request->user();
//        dd($user);
        $input = $request->validated();
        $user->employee_id = $input['employee_id'];
        $user->last_name = $input['last_name'];
        $user->first_name = $input['first_name'];
        $user->middle_name = $input['middle_name'];
        $user->suffix = $input['suffix'];
        $user->email = $input['email'];
        $user->companies()->sync($input['company_id']);
        $user->save();

        return $this->sendResponse(new AdminResource($user), 'Admin Account updated successfully.');
    }
}
