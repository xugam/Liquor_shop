<?php

namespace App\Services;

use App\Models\LocationProduct;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Add stock to a location
     */
    public function addStock($productId, $locationId, $quantity, $unitType, $referenceType = null, $referenceId = null, $supplierId = null)
    {
        $locationProduct = LocationProduct::where('product_id', $productId)->where('location_id', $locationId)->first();
        $productunit = ProductUnit::where('id', $unitType)->first();
        $basequantity = $productunit->convertToBaseUnits($quantity);
        $locationProduct->quantity = $locationProduct->quantity + $basequantity;
        $locationProduct->save();

        // Record movement
        $stockMovement = StockMovement::create([
            'product_id' => $productId,
            'product_unit_id' => $unitType,
            'from_location_id' => null,
            'to_location_id' => $locationId,
            'supplier_id' => $supplierId,
            'quantity' => $basequantity,
            'type' => 'IN',
        ]);

        return $stockMovement;
    }

    /**
     * Deduct stock from a location
     */
    public function deductStock($productId, $locationId, $quantity, $unitType, $referenceType = null, $referenceId = null, $notes = null)
    {
        $product = Product::findOrFail($productId);

        // Convert to base units
        $quantityInBaseUnits = $product->convertToBaseUnits($quantity, $unitType);

        // Get stock
        $stock =    LocationProduct::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->firstOrFail();

        // Check availability
        if ($stock->quantity < $quantityInBaseUnits) {
            throw new \Exception("Insufficient stock for product ID {$productId}. Available: {$stock->quantity}, Requested: {$quantityInBaseUnits}");
        }

        // Deduct stock
        $stock->decrement('quantity', $quantityInBaseUnits);

        // Record movement
        StockMovement::create([
            'product_id' => $productId,
            'location_id' => $locationId,
            'quantity' => -$quantityInBaseUnits, // Negative for OUT
            'movement_type' => 'OUT',
            'unit_used_for_entry' => $unitType,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            // 'created_by' => auth()->id()
        ]);

        return $stock;
    }

    /**
     * Transfer stock between locations
     */
    public function transferStock($productId, $fromLocationId, $toLocationId, $quantity, $unitType, $notes = null)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($productId);
            $quantityInBaseUnits = $product->convertToBaseUnits($quantity, $unitType);

            // Deduct from source
            $this->deductStock($productId, $fromLocationId, $quantity, $unitType, null, null, "Transfer to location {$toLocationId}: {$notes}");

            // Add to destination
            $this->addStock($productId, $toLocationId, $quantity, $unitType, null, null, "Transfer from location {$fromLocationId}: {$notes}");

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get current stock at location
     */
    public function getStock($productId, $locationId)
    {
        $stock = LocationProduct::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->first();

        return $stock ? $stock->quantity : 0;
    }

    /**
     * Check if stock is available
     */
    public function checkAvailability($productId, $locationId, $quantity, $unitType)
    {
        $product = Product::findOrFail($productId);
        $quantityInBaseUnits = $product->convertToBaseUnits($quantity, $unitType);

        $currentStock = $this->getStock($productId, $locationId);

        return [
            'available' => $currentStock >= $quantityInBaseUnits,
            'current_stock' => $currentStock,
            'requested' => $quantityInBaseUnits,
            'shortage' => max(0, $quantityInBaseUnits - $currentStock)
        ];
    }
}
