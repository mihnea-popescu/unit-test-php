<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorize all
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['exists:App\Models\Category,id'],
            'name'  => ['string', 'min:5', 'max:250'],
            'description' => ['nullable', 'string', 'min:5', 'max: 250'],
            'stock' => ['integer', 'min:0'],
            'price' => ['numeric', 'min:0.1', 'regex:/^\d+(\.\d{1,2})?$/'],
            'sale_price' => ['nullable', 'numeric', 'min:0.1', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }
}
