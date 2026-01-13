<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandListResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brand = Brand::all();
        $data = BrandListResource::collection($brand);
        return response()->json($data, 200);
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
}
