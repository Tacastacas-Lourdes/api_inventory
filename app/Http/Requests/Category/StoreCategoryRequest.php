<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Body parameters
 */
class StoreCategoryRequest extends FormRequest
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
            'name' => 'required',
            'spec' => 'array|required',
        ];
    }

    /**
     * @return string[][]
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Name of the Category',
            ],
            'spec' => [
                'description' => 'The specification details',
                'example' => 'CPU',
            ],
        ];
    }
}
