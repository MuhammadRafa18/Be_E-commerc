<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\BannerResource;
use App\Models\Banner as ModelsBanner;
use Illuminate\Support\Facades\Validator;

class Banner extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Banner = ModelsBanner::get();
        if($Banner->count()){
              return BannerResource::collection($Banner);
        }else{
            return response()->json(['message' => 'Data not Found'], 401);
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
            'banner' => 'required|image|max:2048',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('banner')) {
            $Imagebanner = $request->file('banner')->store('banners', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $Banner = ModelsBanner::create([
            'banner' => $Imagebanner,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new BannerResource($Banner)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelsBanner $Banner)
    {
        return new BannerResource($Banner);
    }

    /**
     * Show the form for editing the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsBanner $Banner)
    {
        $validasi = Validator::make($request->all(), [
            'banner' => 'required|image|max:2048',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('banner')) {
            $Imagebanner = $request->file('banner')->store('banners', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $Banner->update([
            'banner' => $Imagebanner,
        ]);
        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new BannerResource($Banner)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsBanner $Banner)
    {
         $Banner->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}
