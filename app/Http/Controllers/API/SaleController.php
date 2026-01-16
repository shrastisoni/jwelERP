<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\StockLedger;
use App\Models\LedgerEntry;

class SaleController extends Controller
{
    /**
     * List all sales
     */
    // public function index()
    // {
    //     return Sale::with('items.product', 'party')
    //         ->latest()
    //         ->get();
    // }
    public function index(Request $request)
    {
        $q = Sale::with('party');

        if ($request->from_date) {
            $q->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $q->whereDate('invoice_date', '<=', $request->to_date);
        }

        if ($request->party_id) {
            $q->where('party_id', $request->party_id);
        }

        if ($request->search) {
            $q->where('invoice_no', 'like', "%{$request->search}%");
        }

        return $q->orderBy('invoice_date', 'desc')->get();
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
                'message' => $e->getMessage(),
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
                    $product = Product::where('id', $item['product_id'])
                        ->where('is_active', true)
                        ->first();

                    if (!$product) {
                        throw ValidationException::withMessages([
                            'product' => 'Inactive product cannot be used'
                        ]);
                    }
                    // ❌ INSUFFICIENT STOCK
                    if (
                        $stock->quantity < $item['quantity'] ||
                        $stock->weight < $item['weight']
                    ) {
                        throw ValidationException::withMessages([
                            'stock' => [
                                'Insufficient stock for product Name ' . $product['name']
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
                    LedgerEntry::create([
                        'account_type' => 'party',
                        'account_id'   => $sale->party_id,
                        'date'         => $sale->invoice_date,
                        'voucher_type' => 'sale',
                        'voucher_id'   => $sale->id,
                        'debit'        => $sale->total_amount,
                        'credit'       => 0,
                        'narration'    => 'Sale Invoice #' . $sale->invoice_no
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
                'message' => $e->getMessage(),
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Sale save failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // public function show(Sale $sale)
    // {
    //     return $sale->load([
    //         'party',
    //         'items.product'
    //     ]);
    // }
    public function show($id)
    {
        $sale = Sale::with([
            'party',
            'items.product',
            'ledgers.product'
        ])->findOrFail($id);

        return response()->json($sale);
    }

}
