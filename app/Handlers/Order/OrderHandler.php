<?php

namespace App\Handlers\Order;

use App\Models\Addres;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ZoneRegion;
use Illuminate\Support\Facades\DB;

class OrderHandler implements OrderHandlerInterface
{
    public function checkout($user, array $data): Order
    {

        $addres = Addres::where('id', $data['address_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$addres) {
            throw new \Exception('Alamat tidak ditemukan');
        }

        $region = ZoneRegion::with('shipping_zone')
            ->find($data['zones_region_id']);

        if (!$region) {
            throw new \Exception('Region tidak ditemukan');
        }
        $ongkir = $region->shipping_zone->price;


        return DB::transaction(function () use ($user, $region, $addres, $ongkir) {


            $carts = Cart::where('user_id', $user->id)
                ->where('is_selected', true)
                ->with([
                    'product',
                    'product_sku',
                    'product_skincare',
                    'product_fashion',
                ])
                ->lockForUpdate()
                ->get();
            if ($carts->isEmpty()) {
                throw new \Exception('Pilih produk dulu', 422);
            }

            foreach ($carts as $item) {
                $skuLatest = $item->product_sku()->lockForUpdate()->first();

                if (!$skuLatest || $skuLatest->stock < $item->qty) {
                    throw new \Exception("Gagal Checkout: Stok produk {$item->product->title} tidak mencukupi atau sudah habis.");
                }

                $subtotal += $item->product_sku->sell_price * $item->qty;
                $diskon += ($item->product_sku->price - $item->product_sku->sell_price) * $item->qty;
            }


            $total = $subtotal + $ongkir;


            $order = Order::create([
                'user_id'          => $user->id,
                'address_id'       => $addres->id,
                'zones_region_id'  => $region->id,
                'shipping_name'    => $addres->fullname,
                'shipping_phone'   => $user->phone,
                'shipping_street'  => $addres->streetname,
                'shipping_city'    => $addres->city,
                'shipping_province' => $addres->provinci,
                'subtotal'         => $subtotal,
                'diskon'           => $diskon,
                'ongkir'           => $ongkir,
                'total'            => $total,
                'status'           => 'Pending',
            ]);


            $orderItems = $carts->map(fn($cart) => [
                'order_id'     => $order->id,
                'product_id'    => $cart->product_id,
                'product_sku_id' => $cart->product_sku_id,
                'product_title' => $cart->product->title,
                'product_image' => $cart->product->image_banner,
                'product_size' => $cart->product_fashion->size ?? $cart->product_skincare->size,
                'produk_sell_price' => $cart->product_sku->sell_price,
                'qty'          => $cart->qty,
                'subtotal'     => $cart->product_sku->sell_price * $cart->qty,
                'created_at'   => now(),
                'updated_at'   => now(),
            ])->toArray();

            OrderItem::insert($orderItems);


            Cart::whereIn('id', $carts->pluck('id'))->delete();

            return  $order->load('order_item');
        });
    }

    public function updateStatus(Order $order, array $data): Order
    {
        if (
            $data['status'] === 'Dikirim'
            && empty($data['trackingNumber'])
        ) {
            throw new \Exception(
                'Tracking number wajib diisi saat dikirim'
            );
        }

        $order->update([
            'status' => $data['status'],
            'trackingNumber' => $data['trackingNumber'] ?? null,
        ]);

        return $order;
    }
}
