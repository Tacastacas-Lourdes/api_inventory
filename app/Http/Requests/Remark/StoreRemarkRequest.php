<?php

namespace App\Http\Requests\Remark;

use Illuminate\Foundation\Http\FormRequest;

class StoreRemarkRequest extends FormRequest
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
            'unit' => 'required',
            'name' => 'required',
            'date' => 'required',
        ];
    }
}
