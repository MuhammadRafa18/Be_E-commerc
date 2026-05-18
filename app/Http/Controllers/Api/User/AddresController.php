<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddresResource;
use App\Models\Addres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $addre = Addres::where('user_id', $user->id)->get();
        if ($addre->isEmpty()) {
            return response()->json([
                'messages' => "Address Not Found"
            ], 404);
        }
        return AddresResource::collection($addre);
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'streetname' => 'required|string|max:255',
            'place' => 'required|string|max:255',
            'provinci' => 'required|string|max:255',
            'city' => 'required|string|max:255'
        ]);
        $validator->after(function ($validator) use ($request) {
            $count = Addres::where('user_id', $request->user()->id)->count();

            if ($count >= 3) {
                $validator->errors()->add('address', 'Maksimal 3 alamat');
            }
        });
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id;

        $addre = Addres::create($data);
        return response()->json([
            'messages' => 'Data Berhasil ditambahkan',
            'data' => new AddresResource($addre)
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $address = Addres::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        return response()->json(['data' => new AddresResource($address)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'fullname' => 'sometimes|string|max:255',
            'streetname' => 'sometimes|string|max:255',
            'place' => 'sometimes|string|max:255',
            'provinci' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255'
        ]);
        $addres = Addres::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $addres->update($data);
        return response()->json([
            'messages' => 'Alamat Berhasil diupdate',
            'data' => new AddresResource($addres)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {

        $addres = Addres::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $addres->delete();
        return response()->json([
            'messages' => 'Alamat berhasil dihapus',
        ], 200);
    }
}
