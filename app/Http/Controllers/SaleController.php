<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSalesRequest;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Cheque;
use App\Models\ProductUnit;
use App\Services\StockService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    protected $stockService;
    use ResponseTrait;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Create a new sale (POS)
     */
    public function store(CreateSalesRequest $request)
    {

        $validated = $request->validated();
        // return $validated;
        $user = Auth::user();
        DB::beginTransaction();
        try {
            // 1. Check stock availability for all items FIRST
            foreach ($validated['items'] as $item) {

                $availability = $this->stockService->checkAvailability(
                    $item['unit_id'],
                    $item['location_id'],
                    $item['quantity']
                );

                if ($availability['available'] == false) {
                    throw new \Exception(
                        "Insufficient stock for product ID {$item['product_id']}. " .
                            "Available: {$availability['current_stock']}, " .
                            "Requested: {$availability['requested']}"
                    );
                }
            }

            // 2. Calculate total amount
            $totalAmount = 0;

            // 3. Create sale record
            $sale = Sale::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
            ]);

            // 4. Process each sale item
            foreach ($validated['items'] as $item) {
                $productunit = ProductUnit::find($item['unit_id']);

                // Deduct stock from location
                $this->stockService->deductStock(
                    $item['unit_id'],
                    $item['location_id'],
                    $item['quantity']
                );

                $unit_price = $item['unit_price'] ? $item['unit_price'] : $productunit->selling_price;
                $total_price = $item['unit_price'] ? $item['unit_price'] * $item['quantity'] : $productunit->BaseSellingPrice($item['quantity']);
                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'location_id' => $item['location_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unit_price,
                    'total_price' => $total_price
                ]);
                $totalAmount += $total_price;
            }
            $sale->update([
                'total_amount' => $totalAmount,
            ]);
            $sale->save();

            DB::commit();

            return response()->json([
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->id,
                'total_amount' => $totalAmount,
                'sale' => $sale->load('items.product', 'cheque')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all sales
     */
    public function index(Request $request)
    {
        $query = Sale::with(['items.product', 'cheque']);

        // Filters
        if ($request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($sales);
    }

    /**
     * Get single sale details
     */
    public function show(Sale $sale)
    {
        if ($sale) {
            $sale->load('items.product', 'cheque');
            return $this->apiSuccess('Sale details', $sale);
        }
        return $this->apiError('Sale not found', 404);
    }

    // /**
    //  * Cancel/Void a sale (restores stock)
    //  */
    public function cancel(Sale $sale)
    {
        DB::beginTransaction();
        try {

            // Check if already cancelled
            if ($sale->status === 'cancelled') {
                throw new \Exception('Sale is already cancelled');
            }

            // Restore stock for each item
            $saleItem = SaleItem::where('sale_id', $sale->id)->get();

            foreach ($saleItem as $item) {
                // Restore stock back to location
                $this->stockService->addStock(
                    $item->location_id,
                    $item->quantity,
                    $item->unit_id
                );
            }

            // Mark sale as cancelled
            $sale->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'message' => 'Sale cancelled and stock restored',
                'sale_id' => $sale->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
