<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class StoreProductRequest extends FormRequest
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


    public function rules()
    {

        // ambil category
        $category = Category::findOrFail($this->category_id);

        // base rules
        $rules = [
            'image_produk' => 'required|image|max:2048',
            'image_banner' => 'required|image|max:2048',
            'title' => 'required|string|max:50|unique:product,title',
            'category_id' => 'required|exists:category,id',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight_gram' => 'required|integer|min:0',
        ];

        // kalau category gak ditemukan → stop
        if (!$category) {
            return $rules;
        }

        // 🔹 SKINCARE
        if ($category->type === 'skincare') {
            $rules += [
                'skin_type_id' => 'required|array|min:1',
                'skin_type_id.*' => 'exists:skin_type,id',
                'size' => 'required|string',
                'use_produk' => 'nullable|string|max:1000',
                'ingredient' => 'nullable|string|max:1000',
            ];
        }

        // 🔹 FASHION
        if ($category->type === 'fashion') {
            $rules += [
                'variants' => 'required|array|min:1',
                'variants.*.size' => 'required|string',
                'variants.*.color' => 'required|string',
            ];
        }


        return $rules;
    }



    protected function failedValidation($validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
