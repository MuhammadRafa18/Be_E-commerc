<?php

namespace App\Http\Controllers;

use App\Http\Resources\ParagrafAboutResource;
use App\Models\ParagrafAbout as ModelsParagrafAbout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ParagrafAbout extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Paragrafabout = ModelsParagrafAbout::get();
        if($Paragrafabout->count()){
              return ParagrafAboutResource::collection($Paragrafabout);
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
            'imageabout' => 'required|image|max:2048',
            'paragrafabout1' => 'required|string|max:1000',
            'paragrafabout2' => 'required|string|max:1000',
            'paragrafabout3' => 'required|string|max:1000',
            'paragrafabout4' => 'required|string|max:1000',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('imageabout')) {
            $ImageAbout = $request->file('imageabout')->store('imageabouts', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $Paragrafabout = ModelsParagrafAbout::create([
            'imageabout' => $ImageAbout,
            'paragrafabout1' => $request->paragrafabout1,
            'paragrafabout2' => $request->paragrafabout2,
            'paragrafabout3' => $request->paragrafabout3,
            'paragrafabout4' => $request->paragrafabout4,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new ParagrafAboutResource($Paragrafabout)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelsParagrafAbout $Paragrafabout)
    {
        return new ParagrafAboutResource($Paragrafabout);
    }

    /**
     * Show the form for editing the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsParagrafAbout $Paragrafabout)
    {
        $validasi = Validator::make($request->all(), [
            'imageabout' => 'required|image|max:2048',
            'paragrafabout1' => 'required|string|max:1000',
            'paragrafabout2' => 'required|string|max:1000',
            'paragrafabout3' => 'required|string|max:1000',
            'paragrafabout4' => 'required|string|max:1000',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('imageabout')) {
            $ImageAbout = $request->file('imageabout')->store('imageabouts', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $Paragrafabout->update([
            'imageabout' => $ImageAbout,
            'paragrafabout1' => $request->paragrafabout1,
            'paragrafabout2' => $request->paragrafabout2,
            'paragrafabout3' => $request->paragrafabout3,
            'paragrafabout4' => $request->paragrafabout4,
        ]);
        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new ParagrafAboutResource($Paragrafabout)
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsParagrafAbout $Paragrafabout)
    {
         $Paragrafabout->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}

