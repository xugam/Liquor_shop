<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductUnitListResource;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{
    public function index()
    {
        $productUnit = ProductUnit::all();
        $data = ProductUnitListResource::collection($productUnit);
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'conversion_factor' => 'required|numeric',
        ]);
        $productUnit = ProductUnit::create($request->all());
        return response()->json($productUnit, 201);
    }
}
