<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use App\Http\Resources\TypeResource;
use Illuminate\Support\Facades\Validator;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $type = Type::get();
        if ($type->count()) {
            return TypeResource::collection($type);
        } else {
            return response()->json(['messages' => 'Data not Found'], 401);
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
            'type' => 'required|string|max:100'
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $type = Type::create([
            'type' => $request->type,
        ]);

        return response()->json([
            'messages' => 'data berhasil ditambah',
            'data' => new TypeResource($type)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Type $type)
    {
        return new TypeResource($type);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Type $type)
    {
        $validasi = Validator::make($request->all(), [
            'type' => 'required|string|max:50'
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $type->update([
            'type' => $request->type,
        ]);

        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new TypeResource($type)
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type)
    {
        $type->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}
