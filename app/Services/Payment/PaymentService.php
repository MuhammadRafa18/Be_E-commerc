<?php

namespace App\Services\Payment;

use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\Payment\MidtransGateway;


class PaymentService
{
   public function __construct(protected MidtransGateway $midtrans) {}
   public function checkout($user, $OrderId)
   {
      return DB::transaction(function () use ($user, $OrderId) {

         $order = Order::where('id', $OrderId)->with('user')->lockForUpdate()->firstOrFail();

         if ($order->user_id !== $user->id) {
            abort(403, 'Unauthorized');
         }

         if ($order->status !== 'Pending') {
            throw new HttpResponseException(response()->json([
               'message' => 'Order tidak bisa dibayar'
            ], 422));
         }
         $existingPayment = $order->payments()
            ->where('transaction_status', 'Pending')
            ->latest()
            ->first();

         if ($existingPayment && $existingPayment->expires_at > now()) {
            return [
               'payment_id' => $existingPayment->id,
               'snap_token' => $existingPayment->snap_token,
            ];
         }

         $midtransOrderId = 'PAY-' . $order->id . '-' . time();


         $payment = Payment::create([
            'order_id'       => $order->id,
            'midtrans_order_id' => $midtransOrderId,
            'gross_amount'   => $order->total,
            'transaction_status' => 'Pending',
            'snap_token'     => null,
            'payload'        => null,
            'expires_at' => now()->addMinutes(15)
         ]);




         $snapToken = $this->midtrans->createSnapToken($payment, $order, $user);


         $payment->update([
            'snap_token' => $snapToken,
         ]);

         return [
            'payment_id' => $payment->id,
            'snap_token' => $snapToken,
         ];
      });
   }
   public function callback(array $payload)
   {
      $serverKey = config('Midtrans.server_key');


      Log::info('MIDTRANS CALLBACK', $payload);
      $signature = hash(
         'sha512',
         $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            $serverKey
      );


      if (!isset($payload['signature_key']) || $signature !== $payload['signature_key']) {
         return abort(403, 'Invalid signature');
      }
      return DB::transaction(function () use ($payload,) {




         $payment = Payment::where('midtrans_order_id', $payload['order_id'])
            ->with('order.zones_region')
            ->firstOrFail();


         if (in_array($payment->transaction_status, ['settlement', 'capture'])) {
            return ['message' => 'Already processed'];
         }

         $transactionStatus = $payload['transaction_status'];
         $paymentType = $payload['payment_type'];
         $fraudStatus = $payload['fraud_status'] ?? null;
         $transactionId = $payload['transaction_id'];


         $payment->update([
            'transaction_id' => $transactionId,
            'payment_type' => $paymentType,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payload' => $payload
         ]);

         $order = $payment->order;

         if (in_array($transactionStatus, ['capture', 'settlement'])) {
            if ($order->status !== 'Paid') {
               return [
                  'message' => 'Already processed'
               ];
            }
            $order->update([
               'status' => 'Paid',
               'estimated_delivery_min' => now()->addDays($order->zones_region->estimasi_min_day),
               'estimated_delivery_max' => now()->addDays($order->zones_region->estimasi_max_day),
            ]);
            DB::afterCommit(function () use ($order) {
               Mail::to('arlivacosmetics@gmail.com')
                  ->queue(new NewOrderNotification($order));
            });
         }


         if ($transactionStatus == 'expire') {
            $order->update([
               'status' => 'Expired'
            ]);
         }

         if ($transactionStatus == 'cancel') {
            $order->update([
               'status' => 'Canceled'
            ]);
         }
      });
   }
}
