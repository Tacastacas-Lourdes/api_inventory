<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'token' => $this->createToken('MyApp')->plainTextToken,
            'employee_id' => $this->employee_id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'suffix' => $this->suffix,
            'role_request' => $this->role_request,
            'company' => $this->company,
            'email' => $this->email,
            'password' => $this->password,
            'approved_at' => $this->approved_at,
            'disapproved_at' => $this->disapproved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
