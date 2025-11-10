<?php

namespace App\Http\Controllers;

use App\Http\Resources\FaqResource;
use App\Models\Faq as ModelsFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class Faq extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Faq = ModelsFaq::get();
        if($Faq->count()){
              return FaqResource::collection($Faq);
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
            'judul' => 'required|string|max:150',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
       
        $Faq = ModelsFaq::create([
            'judul' => $request->judul,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new FaqResource($Faq)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelsFaq $Faq)
    {
        return new FaqResource($Faq);
    }

    /**
     * Show the form for editing the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsFaq $Faq)
      {
        $validasi = Validator::make($request->all(), [
            'judul' => 'required|string|max:150',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
       
        $Faq->update([
            'judul' => $request->judul,
        ]);
        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new FaqResource($Faq)
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsFaq $Faq)
    {
         $Faq->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}
