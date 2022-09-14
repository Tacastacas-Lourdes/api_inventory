<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

/**
 * @group Role Management
 */
class RoleController extends BaseController
{
    public function index(): JsonResponse
    {
        $role = Role::query()->where('name', 'like', '%admin%')->get();
//        $role = Role::all()->except([1,2]);
//            Role::query()->whereIn('name', ['super_admin', 'admin'])->get();
        return $this->sendResponse($role, 'Role retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRoleRequest  $request
     * @return JsonResponse
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $input = $request->validated();
        $role = Role::create($input);
        $role->permissions()->sync($input['permissions']);
        $role->save();

        return $this->sendResponse(new RoleResource($role), 'Role with permissions created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  Role  $role
     * @return JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        return $this->sendResponse(new RoleResource($role), 'Roles retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRoleRequest  $request
     * @param  Role  $role
     * @return JsonResponse
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $input = $request->validated();
        $role->update($input);
        $role->permissions()->sync($input['permissions']);
        $role->save();

        return $this->sendResponse(new RoleResource($role), 'Role with permissions updated successfully.');
    }

//    /**
//     * Display the specified resource.
//     *
//     * @param  Role  $role
//     * @return JsonResponse
//     */
//    public function destroy(Role $role): JsonResponse
//    {
//        $role->delete();
//
//        return $this->sendResponse($role, 'Role deleted.');
//    }
}
