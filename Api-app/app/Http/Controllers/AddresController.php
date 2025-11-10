<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddresResource;
use App\Models\Addres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addre = Addres::get();
        if($addre->count()){
            return AddresResource::collection($addre);
        }else{
            return response()->json([
                'messages' => "Data Not Found"
            ], 401);
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
        $validasi = Validator::make($request->all(),[
             'user_id' => 'required|',
             'fullname' => 'required|string|max:150',
             'streetname' => 'required|string|max:100',
             'place' => 'required|string|max:100',
             'provinci' => 'required|string|max:100',
             'city' => 'required|string|max:100'
        ]);
        if($validasi->fails()){
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }

        $addre = Addres::create([
           'user_id' => $request->user_id,
           'fullname' => $request->fullname,
           'streetname' => $request->streetname,
           'place' => $request->place,
           'provinci' => $request->provinci,
           'city' => $request->city,
        ]);
        return response()->json([
           'messages' => 'Data Berhasil ditambahkan',
           'data' => new AddresResource($addre)
        ], 200);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Addres $addre)
    {
        return new AddresResource($addre);
    }

    /**
     * Show the form for editing the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Addres $addre)
    {
        $validasi = Validator::make($request->all(),[
             'user_id' => 'required|',
             'fullname' => 'required|string|max:150',
             'streetname' => 'required|string|max:100',
             'place' => 'required|string|max:100',
             'provinci' => 'required|string|max:100',
             'city' => 'required|string|max:100'
        ]);
        if($validasi->fails()){
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }

        $addre->update([
           'user_id' => $request->user_id,
           'fullname' => $request->fullname,
           'streetname' => $request->streetname,
           'place' => $request->place,
           'provinci' => $request->provinci,
           'city' => $request->city,
        ]);
        return response()->json([
           'messages' => 'Data Berhasil diupdate',
           'data' => new AddresResource($addre)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Addres $addre)
    {
        $addre->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}
