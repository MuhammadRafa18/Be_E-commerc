<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SkinTypesResource;
use App\Models\SkinType;
use Illuminate\Support\Facades\Storage;

class SkinTypes extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Skin_type = SkinType::orderBy('created_at', 'desc')->get();
        if ($Skin_type->isEmpty()) {
            return response()->json([
                'message' => 'Skin Type not Found',
            ], 404);
        }
        return SkinTypesResource::collection($Skin_type);
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
            'type' => 'required|string|max:255',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('image')) {
            $ImageSkin_type = $request->file('image')->store('skin_type', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $Skin_type = SkinType::create([
            'image' => $ImageSkin_type,
            'type' => $request->type,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new SkinTypesResource($Skin_type)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SkinType $Skin_type)
    {

        return response()->json(['data' => new SkinTypesResource($Skin_type)],200);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SkinType $Skin_type)
    { {
            $validasi = Validator::make($request->all(), [
                'image' => 'sometimes|image|max:2048',
                'type' => 'sometimes|string|max:255',
            ]);
            if ($validasi->fails()) {
                return response()->json([
                    'error' => $validasi->messages(),
                ], 422);
            }
            if ($request->hasFile('image')) {
                if ($Skin_type->image && Storage::disk('public')->exists($Skin_type->image)) {
                    Storage::disk('public')->delete($Skin_type->image);
                }
                $ImageSkin_type = $request->file('image')->store('skin_type', 'public');
                $Skin_type->image = $ImageSkin_type;
            }
            $Skin_type->update([
                'type' => $request->type,
            ]);
            return response()->json([
                'messages' => 'Skin Type berhasil diupdate',
                'data' => new SkinTypesResource($Skin_type)
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SkinType $Skin_type)
    {

        if ($Skin_type->image && Storage::disk('public')->exists($Skin_type->image)) {
            Storage::disk('public')->delete($Skin_type->image);
        }

        $Skin_type->delete();
        return response()->json([
            'message' => 'Skin Type berhasil di hapus',

        ], 200);
    }
}
