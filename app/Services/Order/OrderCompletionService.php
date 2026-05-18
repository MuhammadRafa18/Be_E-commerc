<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderCompletionService
{
    public function complete(Order $order): Order
    {
        return DB::transaction(function () use ($order) {

            $order = Order::where('id', $order->id)
                ->with([
                    'order_item.product_sku'
                ])
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status === 'Selesai') {
                return $order;
            }

            if ($order->status !== 'Dikirim') {
                throw new \Exception('Order belum bisa diselesaikan');
            }

            if ($order->stock_reduced_at === null) {
                foreach ($order->order_item as $item) {

                    $sku = $item->product_sku()
                        ->lockForUpdate()
                        ->first();

                    if (!$sku) {
                        throw new \Exception('SKU produk tidak ditemukan');
                    }

                    if ($sku->stock < $item->qty) {
                        throw new \Exception("Stok produk {$item->product_title} tidak cukup");
                    }

                    $sku->decrement('stock', $item->qty);
                    $sku->deactivateIfStockOut();
                }

                $order->update([
                    'stock_reduced_at' => now(),
                ]);
            }


            $order->update([
                'status' => 'Selesai',
                'completed_at' => now(),
            ]);

            return $order->load('order_item.product_sku');
        });
    }
}
