<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order as ModelsOrder;
use App\Http\Resources\OrderResource;
use App\Models\Addres;
use App\Models\Produk;
use Illuminate\Support\Facades\Validator;

class Order extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Order = ModelsOrder::with(['addres:id,fullname,streetname,provinci,city', 'produk:id,title,size,price,imagebanner'])
            ->whereIn('status', [
                'Pending',
                'Dipersiapkan',
                'Dalam Pengiriman'
            ])
            ->latest()
            ->get();
        if ($Order->count()) {
            return OrderResource::collection($Order);
        } else {
            return response()->json([
                'messages' => "Data Not Found"
            ], 404);
        }
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $Order = ModelsOrder::with(['addres:id,fullname,streetname,provinci,city', 'produk:id,title,size,price,imagebanner'])
            ->where('user_id', $user->id)
            ->get();
        if ($Order->count()) {
            return OrderResource::collection($Order);
        } else {
            return response()->json([
                'messages' => "Data Not Found"
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'addres_id' => 'required|exists:addres,id',
            'produk_id' => 'required|exists:produk,id',
            'qty' => 'required|numeric|min:1',
            'diskon' => 'required|numeric',
            'ongkir' => 'required|numeric',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $addres = Addres::where('id', $request->addres_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        $produk = Produk::findOrFail($request->produk_id);
        $subtotal = $produk->price * $request->qty;
        $diskon = $request->diskon;
        $ongkir = $request->ongkir;
        $total = $subtotal - $diskon + $ongkir;
        $Order = ModelsOrder::create([
            'user_id' => $request->user()->id,
            'addres_id' => $addres->id,
            'produk_id' => $produk->id,
            'qty' => $request->qty,
            'diskon' => $diskon,
            'ongkir' => $ongkir,
            'total' => $total,
            'status' => 'Pending',
        ]);
        return response()->json([
            'messages' => 'Data Berhasil ditambahkan',
            'data' => new OrderResource($Order)
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $order = ModelsOrder::with(['addres:id,fullname,streetname,provinci,city', 'produk:id,title,size,price,imagebanner'])
            ->where('id', $id)
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
        $validasi = Validator::make($request->all(), [
            'status' => 'required|string|in:Dipersiapkan,Dalam Pengiriman,Selesai',
            'trackingNumber' => 'nullable|string'
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }

        $order->update([
            'status' => $request->status,
            'trackingNumber' => $request->trackingNumber
        ]);
        return response()->json([
            'messages' => 'Data Berhasil diupdate',
            'data' => new OrderResource($order)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsOrder $order)
    {
        if ($order->status !== 'Pending') {
            return response()->json([
                'message' => 'Order tidak dapat dibatalkan pada status ini'
            ], 403);
        } else {
            $order->delete();
            return response()->json([
                'messages' => 'data berhasil dihapus',
            ], 200);
        }
    }
}
