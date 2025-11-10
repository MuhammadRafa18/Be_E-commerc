<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProdukResource;
use App\Models\Produk;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::with(['type:id,type', 'category:id,category',])
        ->latest()
        ->get();
        if ($produk->count()) {
            return ProdukResource::collection($produk);
        } else {
            return response()->json(['messages' => 'Data Not found'], 401);
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
            'imageproduk' => 'required|image|max:2048',
            'imagebanner' => 'required|image|max:2048',
            'title' => 'required|string|max:50',
            'type_id' => 'required',
            'category_id' => 'required',
            'price' =>  'required|numeric|min:0',
            'size' =>   'required|string|max:50',
            'rating' =>  'required|numeric|between:0,5',
            'stok' =>   'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'useproduk' => 'nullable|string|max:1000',
            'ingredient' => 'nullable|string|max:1000',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('imageproduk') && $request->hasFile('imagebanner')) {
            $Imageproduk = $request->file('imageproduk')->store('imageproduks', 'public');
            $Imagebanner = $request->file('imagebanner')->store('imagebanners', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $produk = Produk::create([
            'imageproduk' => $Imageproduk,
            'imagebanner' => $Imagebanner,
            'title' => $request->title,
            'type_id' => $request->type_id,
            'category_id' => $request->category_id,
            'price' =>  $request->price,
            'size' =>   $request->size,
            'rating' =>  $request->rating,
            'stok' =>   $request->stok,
            'description' => $request->description,
            'useproduk' => $request->useproduk,
            'ingredient' => $request->ingredient,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new ProdukResource($produk)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $produk =  Produk::with(['type:id,type', 'category:id,category',])->find($id);
        return new ProdukResource($produk);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $validasi = Validator::make($request->all(), [
            'imageproduk' => 'nullable|image|max:2048',
            'imagebanner' => 'nullable|image|max:2048',
            'title' => 'required|string|max:50',
            'type_id' => 'required',
            'category_id' => 'required',
            'price' =>  'required|numeric|min:0',
            'size' =>   'required|string|max:50',
            'rating' =>  'required|numeric|between:0,5',
            'stok' =>   'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'useproduk' => 'required|string|max:1000',
            'ingredient' => 'required|string|max:1000',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('imageproduk')) {
            $Imageproduk = $request->file('imageproduk')->store('imageproduks', 'public');
            $produk->imageproduk = $Imageproduk;

        }
        if($request->hasFile('imagebanner')){
            $Imagebanner = $request->file('imagebanner')->store('imagebanners', 'public');
            $produk->imagebanner = $Imagebanner;
           
        } 
        $produk->update([
            'title' => $request->title,
            'type_id' => $request->type_id,
            'category_id' => $request->category_id,
            'price' =>  $request->price,
            'size' =>   $request->size,
            'rating' =>  $request->rating,
            'stok' =>   $request->stok,
            'description' => $request->description,
            'useproduk' => $request->useproduk,
            'ingredient' => $request->ingredient,
        ]);
        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new ProdukResource($produk)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();
        return response()->json([
            'message' => 'Data berhasil di hapus',

        ], 200);
    }
}
