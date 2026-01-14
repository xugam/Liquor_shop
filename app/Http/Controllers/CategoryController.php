<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryListResource;
use App\Http\Resources\ProductListResource;
use App\Models\Category;
use App\Models\Product;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $category = Category::all();
        $data = CategoryListResource::collection($category);
        return $this->apiSuccess("Category list", $data);
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
            return $this->apiSuccess("Category found successfully", $data);
        } else {
            return $this->apiError("Category not found");
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
        return $this->apiSuccess("Category created successfully", $category);
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
            return $this->apiSuccess("Category updated successfully", $category);
        } else {
            return $this->apiError("Category not found");
        }
    }
    public function destroy(Category $category)
    {
        if ($category) {
            $category->delete();
            return $this->apiSuccess("Category deleted successfully");
        } else {
            return $this->apiError("Category not found");
        }
    }
}
