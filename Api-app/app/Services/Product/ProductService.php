<?php

namespace App\Services\Product;

use App\Models\Category;
use App\Handlers\Product\SkincareProductHandler;
use App\Handlers\Product\FashionProductHandler;

class ProductService
{
    public function store($request)
    {
        // 1. ambil category
        $category = Category::findOrFail($request['category_id']);

        // 2. tentukan handler
        $handler = $this->resolveHandler($category['type']);
        // 3. eksekusi
        return $handler->store($request, $category);
    }

    public function update($request,$product){
        $category = $product->category ;

        $handler = $this->resolveHandler($category->type);

        return $handler->update($request,$product,$category);
    }

    protected function resolveHandler($type)
    {
        return match ($type) {
            'skincare' => app(SkincareProductHandler::class),
            'fashion' => app(FashionProductHandler::class),
            default => throw new \Exception('Tipe tidak valid'),
        };
    }
}
