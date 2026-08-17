<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function  clearProductCache(Product $product)
    {
        Cache::forget("product_id_{$product->id}");
        if ($product->slug) {
            Cache::forget("product_slug_{$product->slug}");
        }
        Cache::forget('products_page_' . request('page', 1));
    }
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearProductCache($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearProductCache($product);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearProductCache($product);
    }

}
