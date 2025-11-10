<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailFaqResource;
use App\Models\DetailFaq as ModelsDetailFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DetailFaq extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Detailfaq = ModelsDetailFaq::with('faq:id,judul')->latest()->get();
        if($Detailfaq->count()){
              return DetailFaqResource::collection($Detailfaq);
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
            'faq_id' => 'required',
            'quest' => 'required|string|max:150',
            'faq_id' => 'required|string|max:1000',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
       
        $Detailfaq = ModelsDetailFaq::create([
            'faq_id' => $request->faq_id,
            'quest' => $request->quest,
            'answer' => $request->answer,
        ]);
        return response()->json([
            'messages' => 'data berhasil ditambahkan',
            'data' => new DetailFaqResource($Detailfaq)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $Detailfaq = ModelsDetailFaq::with(['faq:id,judul'])->find($id);
        return new DetailFaqResource($Detailfaq);
    }

    /**
     * Show the form for editing the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsDetailFaq $Detailfaq)
      {
        $validasi = Validator::make($request->all(), [
            'faq_id' => 'required',
            'quest' => 'required|string|max:150',
            'faq_id' => 'required|string|max:1000',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages(),
            ], 422);
        }
       
        $Detailfaq->update([
            'faq_id' => $request->faq_id,
            'quest' => $request->quest,
            'answer' => $request->answer,
        ]);
        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new DetailFaqResource($Detailfaq)
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsDetailFaq $Detailfaq)
    {
         $Detailfaq->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}
