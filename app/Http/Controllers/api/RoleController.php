<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\API\BaseController as BaseController;

class RoleController extends BaseController
{
    public function index()
    {
        $role = Role::all();
        if (is_null($role)) {
            return $this->sendError('No Record.');
        }
        return $this->sendResponse($role, 'Role retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|unique:roles',
            'permissions' => 'nullable|array',
            'permissions.*' => "integer|exists:permissions,id",
        ]);
        if($request->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $role = Role::create($input);
        $role->permissions()->sync($request->permissions);
        $role->save();
        return $this->sendResponse($role, 'Role with permissions created successfully.');
    }
    public function update(Request $request, Role $role)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'permissions' => 'nullable|array',
            'permissions.*' => "integer|exists:permissions,id",
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $role->update($input);
        $role->permissions()->sync($input['permissions']);
        $role->save();
        return $this->sendResponse($role, 'Role with permissions updated successfully.');
    }
    public function destroy(Role $role)
    {
        $role->delete();
        return $this->sendResponse([], 'Role deleted.');
    }
}
