<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryListResource;
use App\Http\Resources\ProductListResource;
use App\Models\Category;
use App\Models\Product;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseTrait;
    use PaginationTrait;
    public function index(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);
        $query = Category::query();
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        return $this->apiSuccessPaginated("Category list", CategoryListResource::collection($query->paginate($per_page)));
    }

    //show products by specific category
    public function show(Request $request, $id)
    {
        $category = Category::find($id);

        if ($category) {
            $search = $request->input('search');
            $per_page = $request->input('per_page', 10);
            // return $search;
            $query = Product::with('brand', 'category');
            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }
            $collection = ProductListResource::collection($query->where('category_id', $id)->paginate($per_page));
            if ($collection->isEmpty()) {
                return $this->apiError("No product found in this category");
            }
            return $this->apiSuccessPaginated("Category found successfully", $collection);
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
            $category->clearMediaCollection('category_images');
            return $this->apiSuccess("Category deleted successfully");
        } else {
            return $this->apiError("Category not found");
        }
    }
}
