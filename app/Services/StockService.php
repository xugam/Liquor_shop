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
    public function addStock($productId, $locationId, $quantity, $unitType, $supplierId = null, $remarks = null)
    {
        $locationProduct = LocationProduct::where('unit_id', $unitType)->where('location_id', $locationId)->first();
        $productunit = ProductUnit::where('id', $unitType)->first();
        $basequantity = $productunit->convertToBaseUnits($quantity);
        $locationProduct->quantity = $locationProduct->quantity + $basequantity;
        $locationProduct->save();

        // Record movement
        $stockMovement = StockMovement::create([
            'product_id' => $productunit->product_id,
            'product_unit_id' => $unitType,
            'from_location_id' => null,
            'to_location_id' => $locationId,
            'supplier_id' => $supplierId,
            'quantity' => $basequantity,
            'type' => 'IN',
            'remarks' => $remarks
        ]);

        return $stockMovement;
    }

    /**
     * Deduct stock from a location
     */
    public function deductStock($productId, $locationId, $quantity, $unitType, $supplierId = null, $remarks = null)
    {
        $locationProduct = LocationProduct::where('unit_id', $unitType)->where('location_id', $locationId)->first();
        $productunit = ProductUnit::where('id', $unitType)->first();
        $basequantity = $productunit->convertToBaseUnits($quantity);
        $locationProduct->quantity = $locationProduct->quantity - $basequantity;
        $locationProduct->save();

        // Record movement
        $stockMovement = StockMovement::create([
            'product_id' => $productId,
            'product_unit_id' => $unitType,
            'from_location_id' => $locationId,
            'to_location_id' => null,
            'supplier_id' => $supplierId,
            'quantity' => $basequantity,
            'type' => 'OUT',
            'remarks' => $remarks
        ]);

        return $stockMovement;
    }

    /**
     * Transfer stock between locations
     */
    public function transferStock($productId, $fromLocationId, $toLocationId, $quantity, $unitType, $supplierId = null, $remarks = null)
    {
        DB::beginTransaction();
        try {
            $fromlocationProduct = LocationProduct::where('product_id', $productId)->where('location_id', $fromLocationId)->first();
            $tolocationProduct = LocationProduct::where('product_id', $productId)->where('location_id', $toLocationId)->first();
            if ($fromlocationProduct == null || $tolocationProduct == null) {
                throw new \Exception('No stock of that product in that location');
            }


            $productunit = ProductUnit::where('id', $unitType)->first();
            $basequantity = $productunit->convertToBaseUnits($quantity);
            if ($fromlocationProduct->quantity < $basequantity) {
                throw new \Exception('Insufficient stock in that location. Only left - ' . $fromlocationProduct->quantity);
            }
            $fromlocationProduct->quantity = $fromlocationProduct->quantity - $basequantity;
            $fromlocationProduct->save();
            $tolocationProduct->quantity = $tolocationProduct->quantity + $basequantity;
            $tolocationProduct->save();
            // Record movement
            $stockMovement = StockMovement::create([
                'product_id' => $productId,
                'product_unit_id' => $unitType,
                'from_location_id' => $fromLocationId,
                'to_location_id' => $toLocationId,
                'supplier_id' => $supplierId,
                'quantity' => $basequantity,
                'type' => 'TRANSFER',
                'remarks' => $remarks
            ]);
            DB::commit();
            return $stockMovement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get current stock at location
     */
    public function getStock($unit_id, $locationId)
    {
        $stock = LocationProduct::where('unit_id', $unit_id)
            ->where('location_id', $locationId)
            ->first();

        return $stock ? $stock->quantity : 0;
    }

    /**
     * Check if stock is available
     */
    public function checkAvailability($unit_id, $locationId, $quantity)
    {
        $productunit = ProductUnit::where('id', $unit_id)->first();

        $basequantity = $productunit->convertToBaseUnits($quantity);
        $currentStock = $this->getStock($unit_id, $locationId);

        if ($currentStock < $basequantity) {
            return [
                'available' => false,
                'current_stock' => $currentStock,
                'requested' => $basequantity
            ];
        } else {
            return [
                'available' => true,
                'current_stock' => $currentStock,
                'requested' => $basequantity
            ];
        }
    }
}
