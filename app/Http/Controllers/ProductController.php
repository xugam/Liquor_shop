<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Resources\ProductListResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();
        $data = ProductListResource::collection($product);
        return response()->json($data, 200);
    }

    public function store(ProductStoreRequest $request)
    {
        $request->validated();
        $product = Product::create($request->all());
        if ($request->hasFile('image')) {
            $product
                ->addMedia($request->file('image'))
                ->toMediaCollection('product_images');
        }
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json($product, 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function update(Product $product, Request $request)
    {
        if ($product) {
            $product->update($request->all());
            return response()->json($product, 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
