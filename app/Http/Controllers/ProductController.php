<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductStoreRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(ProductStoreRequest $product)
    {
        return $product->all();
        if ($product->all()) {

            return response()->json(201);
        }
    }
}
