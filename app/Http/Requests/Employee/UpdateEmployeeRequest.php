<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'min:8',
            'suffix' => 'min:5',
            'email' => 'required|email',
            //            'company' => 'exists:roles,company',
            //            'password' => 'required|same:confirmPassword',
        ];
    }
}
