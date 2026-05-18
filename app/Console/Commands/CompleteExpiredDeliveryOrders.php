<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Order\OrderCompletionService;
use Illuminate\Console\Command;

class CompleteExpiredDeliveryOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto complete paid orders after estimated delivery max date';

    /**
     * Execute the console command.
     */
    public function handle(OrderCompletionService $service)
    {
        $orders = Order::where('status', 'Dikirim')->whereNotNull('estimated_delivery_max')->where('estimated_delivery_max', '<=', now())
            ->get();
     
        foreach ($orders as $order) {
            try {
                $service->complete($order);
            } catch (\Exception $e) {
                logger()->error('Auto complete order failed', [
                    'order_id' => $order->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }
        return self::SUCCESS;
    }
}
