<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\StockLedger;


class SaleController extends Controller
{
    /**
     * List all sales
     */
    public function index()
    {
        return Sale::with('items.product', 'party')
            ->latest()
            ->get();
    }

    /**
     * Store new sale
     */
    public function store(Request $request)
    {
        // ✅ VALIDATION
        try {
            $request->validate([
                'party_id' => 'required|exists:parties,id',
                'invoice_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.weight' => 'required|numeric|min:0.001',
                'items.*.rate' => 'required|numeric|min:1',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request) {

                // ✅ CREATE SALE
                $sale = Sale::create([
                    'party_id'    => $request->party_id,
                    'invoice_no'  => 'SAL-' . now()->format('YmdHis'),
                    'invoice_date'=> $request->invoice_date,
                    'total_amount'=> 0
                ]);

                $total = 0;

                foreach ($request->items as $item) {

                    // ✅ GET STOCK (SAFE)
                    $stock = Stock::firstOrCreate(
                        ['product_id' => $item['product_id']],
                        ['quantity' => 0, 'weight' => 0]
                    );

                    // ❌ INSUFFICIENT STOCK
                    if (
                        $stock->quantity < $item['quantity'] ||
                        $stock->weight < $item['weight']
                    ) {
                        throw ValidationException::withMessages([
                            'stock' => [
                                'Insufficient stock for product ID ' . $item['product_id']
                            ]
                        ]);
                    }

                    $amount = $item['weight'] * $item['rate'];

                    // ✅ SALE ITEM
                    SaleItem::create([
                        'sale_id'   => $sale->id,
                        'product_id'=> $item['product_id'],
                        'quantity'  => $item['quantity'],
                        'weight'    => $item['weight'],
                        'rate'      => $item['rate'],
                        'amount'    => $amount,
                    ]);

                    // 🔻 STOCK DECREMENT
                    $stock->decrement('quantity', $item['quantity']);
                    $stock->decrement('weight', $item['weight']);

                    $total += $amount;
                    StockLedger::create([
                        'product_id'      => $item['product_id'],
                        'type'            => 'sale',
                        'reference_id'    => $sale->id,
                        'qty_in'          => 0,
                        'qty_out'         => $item['quantity'],
                        'weight_in'       => 0,
                        'weight_out'      => $item['weight'],
                        'balance_qty'     => $stock->quantity,
                        'balance_weight'  => $stock->weight,
                    ]);

                }

                // ✅ UPDATE TOTAL
                $sale->update([
                    'total_amount' => $total
                ]);
            });

            return response()->json([
                'message' => 'Sale saved successfully'
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Stock validation failed',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Sale save failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
