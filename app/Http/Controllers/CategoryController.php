<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryListResource;
use App\Http\Resources\ProductListResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        $data = CategoryListResource::collection($category);
        return response()->json($data, 200);
    }

    //show products by specific category
    public function show($id)
    {
        $category = Category::find($id);
        if ($category) {
            $collection = ProductListResource::collection(Product::where('category_id', $id)->get());
            $data = $collection->toArray(request());
            foreach ($data as &$item) {
                unset($item['category']);
            }
            return response()->json($data, 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
        ]);
        $category = Category::create($request->all());
        if ($request->hasFile('image')) {
            $category
                ->addMedia($request->file('image'))
                ->toMediaCollection('category_images');
        }
        return response()->json($category, 201);
    }

    public function update(Category  $category, Request $request)
    {
        if ($category) {
            $category->update($request->all());
            if ($request->hasFile('image')) {
                $category->clearMediaCollection('category_images');
                $category
                    ->addMedia($request->file('image'))
                    ->toMediaCollection('category_images');
            }
            return response()->json($category, 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
    public function destroy(Category $category)
    {
        if ($category) {
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
}
