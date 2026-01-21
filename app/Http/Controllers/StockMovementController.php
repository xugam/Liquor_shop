<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stock\addStockRequest;
use App\Http\Requests\Stock\TransferStockRequest;
use App\Models\StockMovement;
use App\Services\StockService;
use App\Traits\ResponseTrait;

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
        // return $validated;
        $stockMovement = $this->stockService->addStock(
            $validated['product_id'],
            $validated['location_id'],
            $validated['quantity'],
            $validated['unit_id']
        );

        if ($stockMovement) {
            return $this->apiSuccess('Stock added successfully', $stockMovement);
        }
        return $this->apiError('Stock added failed', 400);
    }

    public function transfer(TransferStockRequest $request)
    {

        $validated = $request->validated();
        if ($validated['from_location_id'] == $validated['to_location_id']) {
            return $this->apiError('From and to location cannot be same', 400);
        }
        $stockMovement = $this->stockService->transferStock(
            $validated['product_id'],
            $validated['from_location_id'],
            $validated['to_location_id'],
            $validated['quantity'],
            $validated['unit_id']
        );

        if ($stockMovement) {
            return $this->apiSuccess('Stock transfered successfully', $stockMovement);
        }
        return $this->apiError('Stock transfered failed', 400);
    }

    public function adjustment(addStockRequest $request)
    {
        $validated = $request->validated();

        //Adjustments mainly -

        $stockMovement = $this->stockService->deductStock(
            $validated['product_id'],
            $validated['location_id'],
            $validated['quantity'],
            $validated['unit_id']
        );

        if ($stockMovement) {
            return $this->apiSuccess('Stock adjusted successfully', $stockMovement);
        }
        return $this->apiError('Stock adjusted failed', 400);
    }

    public function movements()
    {
        $stockMovements = StockMovement::all();
        return $this->apiSuccess('Stock movements', $stockMovements);
    }

    public function movement($type)
    {
        $stockMovements = StockMovement::where('type', $type)->get();
        if (!$stockMovements->isEmpty()) {
            return $this->apiSuccess('Stock movements', $stockMovements);
        }
        return $this->apiError('Stock movements not found', 404);
    }
}
