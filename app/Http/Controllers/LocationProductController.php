<?php

namespace App\Http\Controllers;

use App\Models\LocationProduct;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class LocationProductController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        return $this->apiSuccess('Location Products', LocationProduct::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:product_units,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
        ]);

        return $this->apiSuccess('Location Product created successfully', LocationProduct::create($validated));
    }

    public function show(Request $request, $id)
    {
        $locationProduct = LocationProduct::find($id);
        if ($locationProduct) {
            $search = $request->input('search');
            $per_page = $request->input('per_page', 10);
            $query = Product::with('name');
            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }
            $collection = LocationProduct::collection($query->where('location_id', $id)->paginate($per_page));
            if ($collection->isEmpty()) {
                return $this->apiError("No product found in this category");
            }
            return $this->apiSuccessPaginated("Category found successfully", $collection);
        } else {
            return $this->apiError("Category not found");
        }
    }

    public function update(Request $request, LocationProduct $locationProduct)
    {
        if ($request->has('quantity')) {
            $locationProduct->quantity = $request->quantity;
        }
        if ($request->has('reorder_level')) {
            $locationProduct->reorder_level = $request->reorder_level;
        }
        $locationProduct->save();
        return $this->apiSuccess($locationProduct);
    }

    public function destroy(LocationProduct $locationProduct)
    {
        $locationProduct->delete();
        return $this->apiSuccess('Location Product deleted successfully');
    }

    public function stockLevel()
    {
        $locationProducts = LocationProduct::all();
        $totalAllowStock = 0;
        $totalReorderStock = 0;
        $totalZeroStock = 0;
        foreach ($locationProducts as $locationProduct) {
            if ($locationProduct->quantity > $locationProduct->reorder_level) {
                $totalAllowStock += 1;
            } elseif ($locationProduct->quantity == 0) {
                $totalZeroStock += 1;
            } else {
                $totalReorderStock += 1;
            }
        }
        $data = [
            'totalAllowStock' => $totalAllowStock,
            'totalReorderStock' => $totalReorderStock,
            'totalZeroStock' => $totalZeroStock
        ];
        return $this->apiSuccess('Stock Level', $data);
    }

    public function stockLevelByProduct($id)
    {
        $units = ProductUnit::where('product_id', $id)->get();
        $stock = 0;
        foreach ($units as $unit) {
            $locationProducts = LocationProduct::where('unit_id', $unit->id)->get();

            foreach ($locationProducts as $locationProduct) {
                $stock += $locationProduct->quantity;
            }
        }
        $data = [
            'stock' => $stock
        ];
        return $this->apiSuccess('Stock Level', $data);
    }

    public function stockLevelByCategory($id)
    {
        $products = Product::where('category_id', $id)->get();
        $stock = 0;
        foreach ($products as $product) {
            $units = ProductUnit::where('product_id', $product->id)->get();
            foreach ($units as $unit) {
                $locationProducts = LocationProduct::where('unit_id', $unit->id)->get();
                foreach ($locationProducts as $locationProduct) {
                    $stock += $locationProduct->quantity;
                }
            }
        }
        $data = [
            'stock' => $stock
        ];
        return $this->apiSuccess('Stock Level', $data);
    }

    public function stockLevelByBrand($id)
    {
        $products = Product::where('brand_id', $id)->get();
        $stock = 0;
        foreach ($products as $product) {
            $units = ProductUnit::where('product_id', $product->id)->get();
            foreach ($units as $unit) {
                $locationProducts = LocationProduct::where('unit_id', $unit->id)->get();
                foreach ($locationProducts as $locationProduct) {
                    $stock += $locationProduct->quantity;
                }
            }
        }
        $data = [
            'stock' => $stock
        ];
        return $this->apiSuccess('Stock Level', $data);
    }
    public function getTotalStockOfProduct()
    {
        $locationProducts = LocationProduct::all();
        $stock = 0;
        foreach ($locationProducts as $locationProduct) {
            $stock += $locationProduct->quantity;
        }
        $data = [
            'stock' => $stock
        ];
        return $this->apiSuccess('Stock Level', $data);
    }
    public function stockLevelByLocation($locationId)
    {
        $locationProducts = LocationProduct::where('location_id', $locationId)->get();
        $stock = 0;

        foreach ($locationProducts as $locationProduct) {
            $stock += $locationProduct->quantity;
        }
        $data = [
            'stock' => $stock
        ];
        return $this->apiSuccess('Stock Level', $data);
    }
}
