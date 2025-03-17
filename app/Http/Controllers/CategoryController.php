<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $categories = category::all();
        return response()->json(CategoryResource::collection($categories), 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $category = Category::create([
                'name' => $validated['name'],
            ]);
            return response()->json(new CategoryResource($category), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            return response()->json(new CategoryResource($category), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $category = Category::find($id);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            $category->update($validated);
            return response()->json(new CategoryResource($category), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }
}
