<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ProdukResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProdukTypeResource;
use App\Models\ProdukType as ModelsProdukType;

class ProdukType extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ProdukType = ModelsProdukType::get();
        if ($ProdukType->count()) {
            return ProdukTypeResource::collection($ProdukType);
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
            'image' => 'required|image|max:2048',
            'type_id' => 'required',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('image')) {
            $ImageProdukType = $request->file('image')->store('images', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $ProdukType = ModelsProdukType::create([
            'image' => $ImageProdukType,
            'type_id' => $request->type_id,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new ProdukTypeResource($ProdukType)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelsProdukType $ProdukType)
    {
        return new ProdukResource($ProdukType);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsProdukType $ProdukType)
    { {
            $validasi = Validator::make($request->all(), [
                'image' => 'required|image|max:2048',
                'type_id' => 'required',
            ]);
            if ($validasi->fails()) {
                return response()->json([
                    'error' => $validasi->messages(),
                ], 422);
            }
            if ($request->hasFile('image')) {
                $ImageProdukType = $request->file('image')->store('images', 'public');
            } else {
                return response()->json([
                    'messages' => 'Gambar Tidak ada'
                ]);
            }
            $ProdukType->update([
                'image' => $ImageProdukType,
                'type_id' => $request->type_id,
            ]);
            return response()->json([
                'messages' => 'data berhasil diupdate',
                'data' => new ProdukTypeResource($ProdukType)
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsProdukType $ProdukType)
    {
        $ProdukType->delete();
        return response()->json([
            'message' => 'Data berhasil di hapus',

        ], 200);
    }
}
