<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductUnitListResource;
use App\Models\ProductUnit;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $productUnit = ProductUnit::all();
        $data = ProductUnitListResource::collection($productUnit);
        return $this->apiSuccess("Product unit list", $data);
    }

    public function update(ProductUnit $productUnit, Request $request)
    {
        if ($productUnit) {
            $productUnit->update($request->all());
            return $this->apiSuccess("Product unit updated successfully", $productUnit);
        } else {
            return $this->apiError("Product unit not found");
        }
    }

    public function destroy(ProductUnit $productUnit)
    {
        if ($productUnit) {
            $productUnit->delete();
            return $this->apiSuccess("Product unit deleted successfully");
        } else {
            return $this->apiError("Product unit not found");
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'slug' => 'required|string|max:30',
            'conversion_factor' => 'required|numeric',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'is_base_unit' => 'required|boolean',
            'product_id' => 'required|exists:products,id',
        ]);
        $productUnit = ProductUnit::create($request->all());
        return $this->apiSuccess("Product unit created successfully", $productUnit);
    }
}
