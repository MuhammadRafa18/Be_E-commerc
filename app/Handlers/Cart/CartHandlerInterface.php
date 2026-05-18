<?php

namespace App\Handlers\Cart;

interface CartHandlerInterface
{
    public function resolveVariant(array $data, $product): array;

}