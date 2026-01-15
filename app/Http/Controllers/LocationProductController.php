<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationProduct;
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
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
        ]);

        return $this->apiSuccess('Location Product created successfully', LocationProduct::create($validated));
    }

    public function show(Location $location)
    {
        return $this->apiSuccess(LocationProduct::where('location_id', $location->id)->get());
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
}
