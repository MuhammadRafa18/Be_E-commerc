<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $product = Product::query()->with([
            'category:id,category,slug,type',
            'skin_type:id,type',
            'product_sku:id,product_id,price,sell_price,stock,weight_gram',
            'product_sku.skincare:id,product_sku_id,size,use_produk,ingredient',
            'product_sku.attribute:id,product_sku_id,size,color',

        ])
            ->latest()
            ->paginate(10);


        if ($product->isEmpty()) {
            return response()->json(['messages' => 'Produk Not found'], 404);
        }
        return ProductResource::collection($product);
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {

        $data = $request->validated();
        if ($request->hasFile('image_produk') && $request->hasFile('image_banner')) {
            $data['image_produk'] = $request->file('image_produk')->store('image_produks', 'public');
            $data['image_banner'] = $request->file('image_banner')->store('image_banner', 'public');
        }



        $product = $this->productService->store($data);

        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $product =  Product::with([
            'category:id,category,slug,type',
            'skin_type:id,type',
            'product_sku:id,product_id,price,sell_price,stock,weight_gram',
            'product_sku.skincare:id,product_sku_id,size,use_produk,ingredient',
            'product_sku.attribute:id,product_sku_id,size,color',
        ])->where('slug', $slug)->firstOrFail();
        if (!$product) {
            return response()->json([
                'message' => 'Produk Not Found'
            ], 404);
        }
        return response()->json([
            'data' => new ProductResource($product)
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {

        $data = $request->validated();
        // \dd($data);
        if ($request->hasFile('image_produk')) {
            if ($product->image_produk && Storage::disk('public')->exists($product->image_produk)) {
                Storage::disk('public')->delete($product->image_produk);
            }
            $data['image_produk'] = $request->file('image_produk')->store('image_produks', 'public');
        }
        if ($request->hasFile('image_banner')) {
            if ($product->image_banner && Storage::disk('public')->exists($product->image_banner)) {
                Storage::disk('public')->delete($product->image_banner);
            }
            $data['image_banner'] = $request->file('image_banner')->store('image_banners', 'public');
        }
        $this->productService->update($data, $product);
        return response()->json([
            'messages' => 'Produk berhasil diupdate',
            'data' => new ProductResource($product)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produk = Product::findOrFail($id);

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
