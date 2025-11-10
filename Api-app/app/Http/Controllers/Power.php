<?php

namespace App\Http\Controllers;

use App\Http\Resources\PowerResource;
use App\Models\Power as ModelsPower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class Power extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Power = ModelsPower::get();
        if ($Power->count()) {
            return PowerResource::collection($Power);
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
            'icon' => 'required|image|max:2048',
            'power' => 'required|string|max:150',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('icon')) {
            $ImageIcon = $request->file('icon')->store('icons', 'public');
        } else {
            return response()->json([
                'messages' => 'Gambar Tidak ada'
            ]);
        }
        $Power = ModelsPower::create([
            'icon' => $ImageIcon,
            'power' => $request->power,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new PowerResource($Power)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelsPower $Power)
    {
        return new PowerResource($Power);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsPower $Power)
   {
        $validasi = Validator::make($request->all(), [
            'icon' => 'nullable|image|max:2048',
            'power' => 'required|string|max:150',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
        if ($request->hasFile('icon')) {
            $ImageIcon = $request->file('icon')->store('icons', 'public');
            $Power->icon = $ImageIcon;
        } 
        $Power->update([
            'power' => $request->power,
        ]);
        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new PowerResource($Power)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsPower $Power)
    {
        $Power->delete();
        return response()->json([
            'message' => 'Data berhasil di hapus',

        ], 200);
    }
}
