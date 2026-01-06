<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;

class CategoryController extends Controller{
    public function index()
    {
        return Category::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return response()->json(['message' => 'Category added']);
    }
   
    // ✅ UPDATE CATEGORY
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id
        ]);

        $category->update(['name' => $request->name]);

        return response()->json(['message' => 'Category updated']);
    }

    // ✅ DELETE CATEGORY (SAFE)
    public function destroy(Category $category)
    {
        $used = Product::where('category_id', $category->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Category in use. Cannot delete.'
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}


