<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stock\addStockRequest;
use App\Http\Requests\Stock\TransferStockRequest;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\StockMovement;
use App\Services\StockService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{

    use ResponseTrait;

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    //IN
    public function incoming(addStockRequest $request)
    {
        $validated = $request->validated();
        $stockMovement = $this->stockService->addStock($validated['product_id'], $validated['location_id'], $validated['quantity'], $validated['unit_id']);

        if ($stockMovement) {
            return $this->apiSuccess('Stock added successfully', $stockMovement);
        }
        return $this->apiError('Stock added failed');
    }

    public function transfer(TransferStockRequest $request)
    {
        $validated = $request->validated();

        $stockMovement = $this->stockService->transferStock($validated['product_id'], $validated['from_location_id'], $validated['to_location_id'], $validated['quantity'], $validated['unit_id']);

        if ($stockMovement) {
            return $this->apiSuccess('Stock transfered successfully', $stockMovement);
        }
        return $this->apiError('Stock transfered failed');
    }

    public function adjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'required|exists:product_units,id', // Bottle/Carton/Pack
        ]);

        //Adjustments mainly -
        $locationProduct = LocationProduct::where('product_id', $validated['product_id'])->where('location_id', $validated['location_id'])->first();
        $productunit = ProductUnit::where('id', $validated['unit_id'])->first();

        $basequantity = $productunit->convertToBaseUnits($validated['quantity']);
        $locationProduct->quantity = $locationProduct->quantity - $basequantity;

        $locationProduct->save();


        // Create stock movement
        $stockMovement = StockMovement::create([
            'product_id' => $validated['product_id'],
            'location_id' => $validated['location_id'],
            'quantity' => $validated['quantity'],
            'type' => 'ADJUSTMENT',
            'product_unit_id' => $validated['unit_id'],
        ]);

        return $this->apiSuccess('Stock adjusted successfully', $stockMovement);
    }

    public function movements()
    {
        $stockMovements = StockMovement::all();
        return $this->apiSuccess('Stock movements', $stockMovements);
    }
}
