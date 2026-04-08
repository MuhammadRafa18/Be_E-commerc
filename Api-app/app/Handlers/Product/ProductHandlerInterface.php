<?php

namespace App\Handlers\Product;

interface ProductHandlerInterface
{
    public function store($request, $category);
    public function update($request, $product, $category);
}