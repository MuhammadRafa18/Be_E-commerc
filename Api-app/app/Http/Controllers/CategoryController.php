<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::get();
        if ($category->count()) {
            return CategoryResource::collection($category);
        } else {
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
            'category' => 'required|string|max:100'
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $category = Category::create([
            'category' => $request->category,
        ]);

        return response()->json([
            'messages' => 'data berhasil ditambah',
            'data' => new CategoryResource($category)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validasi = Validator::make($request->all(), [
            'category' => 'required|string|max:50'
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $category->update([
            'category' => $request->category,
        ]);

        return response()->json([
            'messages' => 'data berhasil diupdate',
            'data' => new CategoryResource($category)
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}
