<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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

        $skuId = $this->product->product_sku->first()->id ?? null;

        // base rules
        $rules = [
            'image_produk' => 'sometimes|image|max:2048',
            'image_banner' => 'sometimes|image|max:2048',
            'title' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('product', 'title')->ignore($this->product->id),
            ],
            'category_id' => 'sometimes|exists:category,id',
            'description' => 'sometimes|string|max:1000',
            'price' => 'sometimes|numeric|min:0',
            'sell_price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'weight_gram' => 'sometimes|integer|min:0',
        ];

        // kalau category gak ditemukan → stop
        if (!$category) {
            return $rules;
        }

        // 🔹 SKINCARE
        if ($category->type === 'skincare') {
            $rules += [
                'skin_type_id' => 'sometimes|array|min:1',
                'skin_type_id.*' => 'exists:skin_type,id',
                'size' => 'sometimes|string',
                'use_produk' => 'sometimes|string|max:1000',
                'ingredient' => 'sometimes|string|max:1000',
            ];
        }

        // 🔹 FASHION
        if ($category->type === 'fashion') {
            $rules += [
                'variants' => 'sometimes|array|min:1',
                'variants.*.id'     => [
                    'sometimes',
                    'integer',
                    // cek id exists di product_fashions DAN milik sku yang benar
                    Rule::exists('product_fashion', 'id')
                        ->where('product_sku_id', $skuId),
                ],

                'variants.*.size' => 'sometimes|string',
                'variants.*.color' => 'sometimes|string',
            ];
        }

        
        return $rules;
    }
    //     protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    // {
    //     dd($validator->errors()->all());
    // }
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
