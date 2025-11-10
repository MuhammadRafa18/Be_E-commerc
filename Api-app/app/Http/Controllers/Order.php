<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order as ModelsOrder;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Validator;

class Order extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Order = ModelsOrder::with(['addres:id,fullname,streetname,provinci,city','produk:id,title,size,price,imagebanner'])->latest()->get();
        if($Order->count()){
            return OrderResource::collection($Order);
        }else{
            return response()->json([
                'messages' => "Data Not Found"
            ], 401);
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
        $validasi = Validator::make($request->all(),[
             'user_id' => 'required|',
             'addres_id' => 'required|',
             'produk_id' => 'required|',
             'qty' => 'required|numeric',
             'diskon' => 'required|numeric',
             'ongkir' => 'required|numeric',
             'total' => 'required|numeric',
             'status' => 'required|string|in:Pending,Dipersiapkan,Dalam Pengiriman,Selesai',
             'trackingNumber' => 'nullable|string'
        ]);
        if($validasi->fails()){
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }

        $Order = ModelsOrder::create([
           'user_id' => $request->user_id,
           'addres_id' => $request->addres_id,
           'produk_id' => $request->produk_id,
           'qty' => $request->qty,
           'diskon' => $request->diskon,
           'ongkir' => $request->ongkir,
           'total' => $request->total,
           'status' => $request->status,
           'trackingNumber' => $request->trackingNumber
        ]);
        return response()->json([
           'messages' => 'Data Berhasil ditambahkan',
           'data' => new OrderResource($Order)
        ], 200);
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = ModelsOrder::with(['addres:id,fullname,streetname,provinci,city','produk:id,title,size,price,imagebanner'])->find($id);
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
             'status' => 'required|string|in:Pending,Dipersiapkan,Dalam Pengiriman,Selesai',
             'trackingNumber' => 'nullable|string'
        ]);
        if($validasi->fails()){
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
        $order->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}