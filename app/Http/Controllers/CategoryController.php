<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryListResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        $data = CategoryListResource::collection($category);
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
        ]);
        $category = Category::create($request->all());
        if ($request->hasFile('image')) {
            $category->addMedia($request->file('image'))->toMediaCollection('category_images');
        }
        return response()->json($category, 201);
    }
}
