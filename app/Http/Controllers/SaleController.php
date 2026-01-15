<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Cheque;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Create a new sale (POS)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'payment_type' => 'required|in:cash,cheque',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_type' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.selling_price' => 'required|numeric|min:0',

            // Cheque details (conditional)
            'cheque.customer_name' => 'required_if:payment_type,cheque',
            'cheque.bank_name' => 'required_if:payment_type,cheque',
            'cheque.cheque_number' => 'required_if:payment_type,cheque',
            'cheque.amount' => 'required_if:payment_type,cheque',
            'cheque.cheque_date' => 'required_if:payment_type,cheque|date',
            'cheque.cashable_date' => 'required_if:payment_type,cheque|date',
        ]);

        DB::beginTransaction();
        try {
            // 1. Check stock availability for all items FIRST
            foreach ($validated['items'] as $item) {
                $availability = $this->stockService->checkAvailability(
                    $item['product_id'],
                    $validated['location_id'],
                    $item['quantity'],
                    $item['unit_type']
                );

                if (!$availability['available']) {
                    throw new \Exception(
                        "Insufficient stock for product ID {$item['product_id']}. " .
                            "Available: {$availability['current_stock']}, " .
                            "Requested: {$availability['requested']}"
                    );
                }
            }

            // 2. Calculate total amount
            $totalAmount = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['selling_price'];
            });

            // 3. Create sale record
            $sale = Sale::create([
                'location_id' => $validated['location_id'],
                'payment_type' => $validated['payment_type'],
                'total_amount' => $totalAmount,
                // 'created_by' => auth()->id()
            ]);

            // 4. Process each sale item
            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);

                // Convert to base units for record keeping
                $quantityInBaseUnits = $product->convertToBaseUnits(
                    $item['quantity'],
                    $item['unit_type']
                );

                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'unit_type' => $item['unit_type'],
                    'quantity_in_selected_unit' => $item['quantity'],
                    'quantity_in_base_units' => $quantityInBaseUnits,
                    'unit_price' => $item['selling_price'],
                    'total_price' => $item['quantity'] * $item['selling_price']
                ]);

                // 5. Deduct stock (uses StockService)
                $this->stockService->deductStock(
                    productId: $item['product_id'],
                    locationId: $validated['location_id'],
                    quantity: $item['quantity'],
                    unitType: $item['unit_type'],
                    referenceType: Sale::class,
                    referenceId: $sale->id,
                    notes: "Sale #{$sale->id}"
                );
            }

            // 6. Handle cheque if payment type is cheque
            if ($validated['payment_type'] === 'cheque') {
                $reminderDate = Carbon::parse($validated['cheque']['cashable_date'])->subDay();

                Cheque::create([
                    'sale_id' => $sale->id,
                    'customer_name' => $validated['cheque']['customer_name'],
                    'bank_name' => $validated['cheque']['bank_name'],
                    'cheque_number' => $validated['cheque']['cheque_number'],
                    'amount' => $validated['cheque']['amount'],
                    'cheque_date' => $validated['cheque']['cheque_date'],
                    'cashable_date' => $validated['cheque']['cashable_date'],
                    'reminder_date' => $reminderDate,
                    'status' => 'pending'
                ]);
            }

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
        $query = Sale::with(['items.product', 'location', 'cheque', 'createdBy']);

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
    public function show($id)
    {
        $sale = Sale::with([
            'items.product.brand',
            'items.product.category',
            'location',
            'cheque',
            'createdBy'
        ])->findOrFail($id);

        return response()->json($sale);
    }

    /**
     * Cancel/Void a sale (restores stock)
     */
    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::with('items')->findOrFail($id);

            // Check if already cancelled
            if ($sale->status === 'cancelled') {
                throw new \Exception('Sale is already cancelled');
            }

            // Restore stock for each item
            foreach ($sale->items as $item) {
                $this->stockService->addStock(
                    productId: $item->product_id,
                    locationId: $sale->location_id,
                    quantity: $item->quantity_in_selected_unit,
                    unitType: $item->unit_type,
                    referenceType: Sale::class,
                    referenceId: $sale->id,
                    notes: "Sale #{$sale->id} cancelled - Stock restored"
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
