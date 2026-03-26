<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\Models\OrderItem;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::with(['skin_type', 'category:id,category,slug',])
            ->latest()
            ->get();
        if ($produk->isEmpty()) {
            return response()->json(['messages' => 'Produk Not found'], 404);
        }
        return ProdukResource::collection($produk);
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
            'category_id' => 'integer|exists:category,id',
            'skin_type_id'   => 'nullable|array',
            'skin_type_id.*' => 'integer|exists:skin_type,id',
            'price' =>  'required|numeric|min:0',
            'sell_price' =>  'required|numeric|min:0',
            'size' =>   'required|numeric|min:0',
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
            'category_id' => $request->category_id,
            'price' =>  $request->price,
            'sell_price' =>  $request->sell_price,
            'size' =>   $request->size,
            'stok' =>   $request->stok,
            'description' => $request->description,
            'useproduk' => $request->useproduk,
            'ingredient' => $request->ingredient,
        ]);

        if ($request->filled('skin_type_id')) {
            $produk->skin_type()->attach($request->skin_type_id);
        }
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new ProdukResource($produk)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $produk =  Produk::with(['skin_type', 'category:id,category',])->where('slug', $slug)->firstOrFail();
        if (!$produk) {
            return response()->json([
                'message' => 'Produk Not Found'
            ], 404);
        }
        return response()->json([
            'data' => new ProdukResource($produk)
        ], 200);
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
            'imageproduk' => 'sometimes|image|max:2048',
            'imagebanner' => 'sometimes|image|max:2048',
            'title' => 'sometimes|required|string|max:50',
            'category_id' => 'sometimes|integer|exists:category,id',
            'skin_type_id'   => 'sometimes|nullable|array',
            'skin_type_id.*' => 'integer|exists:skin_type,id',
            'price' =>  'sometimes|required|numeric|min:0',
            'sell_price' =>  'sometimes|required|numeric|min:0',
            'size' =>   'sometimes|required|string|max:50',
            'stok' =>   'sometimes|required|numeric|min:0',
            'description' => 'sometimes|required|string|max:1000',
            'useproduk' => 'sometimes|required|string|max:1000',
            'ingredient' => 'sometimes|required|string|max:1000',
            'is_active' => 'sometimes|boolean'
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        $data = $validasi->validate();
        if ($request->hasFile('imageproduk')) {
            if ($produk->imageproduk && Storage::disk('public')->exists($produk->imageproduk)) {
                Storage::disk('public')->delete($produk->imageproduk);
            }
            $data['imageproduk'] = $request->file('imageproduk')->store('imageproduks', 'public');
        }
        if ($request->hasFile('imagebanner')) {
            if ($produk->imagebanner && Storage::disk('public')->exists($produk->imagebanner)) {
                Storage::disk('public')->delete($produk->imagebanner);
            }
            $data['imagebanner'] = $request->file('imagebanner')->store('imagebanners', 'public');
        }
        $produk->update($data);
        if ($request->has('skin_type_id')) {
            $produk->skin_type()->sync($request->skin_type_id);
        }
        return response()->json([
            'messages' => 'Produk berhasil diupdate',
            'data' => new ProdukResource($produk)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        $usedInOrder = OrderItem::where('produk_id', $id)->exists();

        if ($usedInOrder) {
            return response()->json([
                'message' => 'Produk tidak bisa dihapus karena sudah ada di order'
            ], 409);
        }

        if ($produk->imageproduk && Storage::disk('public')->exists($produk->imageproduk)) {
            Storage::disk('public')->delete($produk->imageproduk);
        }
        if ($produk->imagebanner && Storage::disk('public')->exists($produk->imagebanner)) {
            Storage::disk('public')->delete($produk->imagebanner);
        }
        $produk->skin_type()->detach($produk->skin_type_id);
        $produk->delete();
        return response()->json([
            'message' => 'Data berhasil di hapus',

        ], 200);
    }
}
