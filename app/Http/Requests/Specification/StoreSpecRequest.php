<?php

namespace App\Http\Requests\Specification;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecRequest extends FormRequest
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
            'category_id' => 'required',
            'spec_name' => 'array',
        ];
    }
}
