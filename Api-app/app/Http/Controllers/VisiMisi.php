<?php

namespace App\Http\Controllers;

use App\Http\Resources\VisiMisiResource;
use App\Models\VisiMisi as ModelsVisiMisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class VisiMisi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Visimisi = ModelsVisiMisi::get();
        if($Visimisi->count()){
              return VisiMisiResource::collection($Visimisi);
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
            'image' => 'required|image|max:2048',
            'visimisi1' => 'required|string|max:1000',
            'visimisi2' => 'required|string|max:1000',
            'visimisi3' => 'required|string|max:1000',
            'visimisi4' => 'required|string|max:1000',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('image')) {
            $ImageVisi = $request->file('image')->store('imagevisi', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $Visimisi = ModelsVisiMisi::create([
            'image' => $ImageVisi,
            'visimisi1' => $request->visimisi1,
            'visimisi2' => $request->visimisi2,
            'visimisi3' => $request->visimisi3,
            'visimisi4' => $request->visimisi4,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new VisiMisiResource($Visimisi)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelsVisiMisi $Visimisi)
    {
        return new VisiMisiResource($Visimisi);
    }

    /**
     * Show the form for editing the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsVisiMisi $Visimisi)
     {
        $validasi = Validator::make($request->all(), [
            'image' => 'nullable|image|max:2048',
            'visimisi1' => 'required|string|max:1000',
            'visimisi2' => 'required|string|max:1000',
            'visimisi3' => 'required|string|max:1000',
            'visimisi4' => 'required|string|max:1000',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('image')) {
            $ImageVisi = $request->file('image')->store('imagevisi', 'public');
            $Visimisi->image = $ImageVisi;
        }
        $Visimisi->update([
            'visimisi1' => $request->visimisi1,
            'visimisi2' => $request->visimisi2,
            'visimisi3' => $request->visimisi3,
            'visimisi4' => $request->visimisi4,
        ]);
        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new VisiMisiResource($Visimisi)
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsVisiMisi $Visimisi)
    {
         $Visimisi->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}
