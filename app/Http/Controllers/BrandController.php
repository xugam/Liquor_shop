<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandListResource;
use App\Http\Resources\ProductListResource;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brand = Brand::all();
        $data = BrandListResource::collection($brand);
        return response()->json($data, 200);
    }

    public function show($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            $collection = ProductListResource::collection(Product::where('brand_id', $id)->get());
            $data = $collection->toArray(request());
            foreach ($data as &$item) {
                unset($item['brand']);
            }
            return response()->json($data, 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'name' => 'required|string|max:30',
        ]);
        $brand = Brand::create($request->all());
        if ($request->hasFile('image')) {
            $brand
                ->addMedia($request->file('image'))
                ->toMediaCollection('brand_images');
        }
        return response()->json($brand, 201);
    }
    public function update(Brand $brand, Request $request)
    {
        if ($brand) {
            $brand->update($request->all());
            if ($request->hasFile('image')) {
                $brand->clearMediaCollection('brand_images');
                $brand
                    ->addMedia($request->file('image'))
                    ->toMediaCollection('brand_images');
            }
            return response()->json($brand, 200);
        } else {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    }
    public function destroy($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            $brand->delete();
            return response()->json(['message' => 'Brand deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    }
}
