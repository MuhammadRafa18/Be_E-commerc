<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'product_id' => 'required|exists:product,id',
            'qty' => 'nullable|integer|min:1',

            // optional karena beda type
            'product_sku_id' => 'nullable|exists:product_sku,id',
            'product_fashion_id' => 'nullable|exists:product_fashion,id',
            'product_skincare_id' => 'nullable|exists:product_skincare,id',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Variant fashion tidak valid',
            ], 422)
        );
    }
}
