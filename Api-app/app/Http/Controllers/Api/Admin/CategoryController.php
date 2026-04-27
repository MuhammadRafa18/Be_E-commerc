<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::orderBy('created_at', 'desc')->get();
        if ($category->isEmpty()) {
            return response()->json(['message' => 'Category not Found'], 404);
        }
        return  CategoryResource::collection($category);
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
            'category' => 'required|string|max:100',
            'type' => 'required|in:skincare,fashion'
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $category = Category::create([
            'category' => $request->category,
            'type' => $request->type,
        ]);

        return response()->json([
            'messages' => 'data berhasil ditambah',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
            'data' => new CategoryResource($category)
        ], 200);
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
            'category' => 'sometimes|string|max:50',
            'type' => 'sometimes|in:skincare,fashion',
            'is_active' => 'sometimes|boolean',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $category->update($validasi->validated());

        return response()->json([
            'messages' => 'Category berhasil diupdate',
            'data' => new CategoryResource($category)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'messages' => 'Category berhasil dihapus',
        ], 200);
    }
}
