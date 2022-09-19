<?php

namespace App\Http\Requests\Unit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
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
            'unit_id' => 'string',
            'brand' => 'string',
            'model' => 'string',
            'serial' => 'string',
            'details' => 'array',
            'remarks' => 'array',
            'company_id' => 'int|exists:companies,id',
            'category_id' => 'int|exists:categories,id',
            'status_id' => 'exists:statuses,id',
        ];
    }
}
