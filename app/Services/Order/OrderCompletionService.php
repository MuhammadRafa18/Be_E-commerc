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

        


            $order->update([
                'status' => 'Selesai',
                'completed_at' => now(),
            ]);

            return $order->load('order_item.product_sku');
        });
    }
}
