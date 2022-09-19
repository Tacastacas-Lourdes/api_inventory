<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'middle_name' => '',
            'suffix' => '',
            'company_id' => 'exists:companies,id',
            'email' => 'required|email',
            //            'password' => 'required|same:confirmPassword',
        ];
    }
}
