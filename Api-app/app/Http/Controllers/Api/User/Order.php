<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order as ModelsOrder;
use App\Http\Resources\OrderResource;
use App\Models\Addres;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\ZoneRegion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Order extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = min($request->input('per_page', 10), 20);
        $Order = ModelsOrder::whereIn('status', [
            'Paid',
            'Diproses',
            'Dikirim',
            'Selesai',
            'Canceled'
        ])
            ->latest()
            ->paginate($perPage);
        if ($Order->isEmpty()) {
            return response()->json(['messages' => "Order Not Found"], 404);
        }
        return OrderResource::collection($Order);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $Order = ModelsOrder::where('user_id', $user->id)->latest()->paginate(10);
        if ($Order->isEmpty()) {
            return response()->json(['messages' => "Data Not Found"], 404);
        }
        return OrderResource::collection($Order);
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function checkout(Request $request)
    {
        $user = $request->user();
        $validasi = Validator::make($request->all(), [
            'address_id' => 'integer|exists:addres,id',
            'zones_region_id' => 'integer|exists:zones_region,id',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'messages' => $validasi->messages()
            ], 422);
        }
        if ($user->phone === null) {
            return response()->json(['message' => 'Verif Nomor Telephone untuk Membuat Order'], 422);
        }
        $addres = Addres::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$addres) {
            return response()->json([
                'message' => 'Alamat tidak ditemukan atau bukan milik anda'
            ], 404);
        }

        $region = ZoneRegion::with('shipping_zone')
            ->where('id', $request->zones_region_id)
            ->first();

        if (!$region) {
            return response()->json([
                'message' => 'Region tidak ditemukan'
            ], 404);
        }
        $ongkir = $region->shipping_zone->price;

        try {
            $order = DB::transaction(function () use ($user, $region, $addres, $ongkir) {

                // Eager load semua relasi sekaligus — hindari N+1
                $carts = Cart::where('user_id', $user->id)
                    ->where('is_selected', true)
                    ->with([
                        'product',
                        'product_sku',
                        'product_skincare',
                        'product_fashion',
                    ]) // ← eager load sku sekalian
                    ->lockForUpdate()
                    ->get();
                if ($carts->isEmpty()) {
                    throw new \Exception('Pilih produk dulu', 422);
                }
                // Hitung subtotal dan diskon
                $subtotal = $carts->sum(
                    fn($item) =>
                    $item->product_sku->sell_price * $item->qty
                );

                $diskon = $carts->sum(
                    fn($item) => ($item->product_sku->price - $item->product_sku->sell_price) * $item->qty // ← kali qty
                );


                $total = $subtotal - $diskon + $ongkir;

                // Buat order
                $order = ModelsOrder::create([
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

                // Insert order items — insert batch hindari loop query
                $orderItems = $carts->map(fn($cart) => [
                    'order_id'     => $order->id,
                    'product_id'    => $cart->product_id,
                    'product_title' => $cart->product->title,
                    'product_size' => $cart->product_fashion->size ?? $cart->product_skincare->size,
                    'produk_sell_price' => $cart->product_sku->price,
                    'qty'          => $cart->qty,
                    'subtotal'     => $cart->product_sku->sell_price * $cart->qty,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ])->toArray();

                OrderItem::insert($orderItems); // ← 1 query, bukan N query

                // Delete cart yang sudah di-lock — pakai id langsung
                Cart::whereIn('id', $carts->pluck('id'))->delete();

                return $order;
            });

            return response()->json([
                'messages' => 'Order Berhasil Dibuat',
                'data'     => $order->load('order_item'),
            ], 201);
        } catch (\Exception $e) {
            // tentukan status code manual
            $code = match ($e->getMessage()) {
                'Pilih produk dulu' => 422,
                default             => 500,
            };

            return response()->json([
                'message' => $e->getMessage()
            ], $code);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {

        $order = ModelsOrder::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        return new OrderResource($order);
    }
        
    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsOrder $order)
    {
        
        $validasi = Validator::make($request->all(),[
            'status' => 'required|in:Diproses,Dikirim,Selesai',
            'trackingNumber' => 'nullable|string'
        ]);
           if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        

        if ($order->status === 'Pending') {
            return response()->json([
                'message' => 'Order belum dibayar'
            ], 400);
        }

        if ($request->status === 'Dikirim' && !$request->trackingNumber) {
            return response()->json([
                'message' => 'Tracking number wajib diisi saat dikirim'
            ], 400);
        }

        $order->update([
            'status' => $request->status,
            'trackingNumber' => $request->trackingNumber
        ]);
        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'data' => $order
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ModelsOrder $order)
    {
        $user = $request->user();
        abort_if($order->user_id !== $user->id, 403);
        if ($order->status !== 'Pending') {
            return response()->json([
                'message' => 'Order tidak dapat dibatalkan '
            ], 403);
        }
        $order->update(['status' => 'Canceled']);
        return response()->json([
            'messages' => 'Order dibatalkan',
        ], 200);
    }
}
