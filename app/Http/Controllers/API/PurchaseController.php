<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\StockLedger;
use App\Models\Product;

use App\Models\LedgerEntry;
use Illuminate\Validation\ValidationException;
class PurchaseController extends Controller
{
    // public function index()
    // {
    //    //old code
    //     // return Purchase::with('items.product','party')->latest()->get();
    // }
    public function index(Request $request)
    {
        $q = Purchase::with('party');

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

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'party_id' => 'required|exists:parties,id',
    //         'invoice_date' => 'required|date',
    //         'items' => 'required|array|min:1',
    //         'items.*.product_id' => 'required|exists:products,id',
    //         'items.*.quantity' => 'required|integer|min:1',
    //         'items.*.weight' => 'required|numeric|min:0.001',
    //         'items.*.rate' => 'required|numeric|min:1',
    //     ]);
    //     try {
    //         DB::transaction(function () use ($request) {

    //             $purchase = Purchase::create([
    //                 'party_id' => $request->party_id,
    //                 'invoice_no' => 'PUR-' . strtoupper(Str::random(8)),
    //                 'invoice_date' => $request->invoice_date,
    //                 'total_amount' => 0,
    //             ]);

    //             $total = 0;

    //             foreach ($request->items as $item) {

    //                 $amount = $item['weight'] * $item['rate'];
    //                 $product = Product::where('id', $item['product_id'])
    //                     ->where('is_active', true)
    //                     ->first();

    //                 if (!$product) {
    //                     throw ValidationException::withMessages([
    //                         'product' => 'Inactive product cannot be used'
    //                     ]);
    //                 }
    //                 PurchaseItem::create([
    //                     'purchase_id' => $purchase->id,
    //                     'product_id' => $item['product_id'],
    //                     'quantity' => $item['quantity'],
    //                     'weight' => $item['weight'],
    //                     'rate' => $item['rate'],
    //                     'amount' => $amount,
    //                 ]);

    //                 // STOCK UPDATE
    //                 $stock = Stock::firstOrCreate(
    //                     ['product_id' => $item['product_id']],
    //                     ['quantity' => 0, 'weight' => 0]
    //                 );

    //                 $stock->increment('quantity', $item['quantity']);
    //                 $stock->increment('weight', $item['weight']);

    //                 $total += $amount;
    //                 StockLedger::create([
    //                     'product_id'      => $item['product_id'],
    //                     'type'            => 'purchase',
    //                     'reference_id'    => $purchase->id,
    //                     'qty_in'          => $item['quantity'],
    //                     'qty_out'         => 0,
    //                     'weight_in'       => $item['weight'],
    //                     'weight_out'      => 0,
    //                     'balance_qty'     => $stock->quantity,
    //                     'balance_weight'  => $stock->weight,
    //                 ]);

    //             }

    //             $purchase->update(['total_amount' => $total]);
    //         });
    //     }catch (ValidationException $e) {
            
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //             'errors'  => $e->errors()
    //         ], 422);

    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'message' => 'Sale save failed',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    //     return response()->json(['message' => 'Purchase created successfully']);
    // }

    public function show($id)
    {
        return Purchase::with('items.product','party')->findOrFail($id);
    }
    public function store(Request $request)
    {
        $request->validate([
            'party_id' => 'required|exists:parties,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.weight' => 'required|numeric|min:0.001',
            'items.*.rate' => 'required|numeric|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $purchase = Purchase::create([
                    'party_id'     => $request->party_id,
                    'invoice_no'   => 'PUR-' . strtoupper(Str::random(8)),
                    'invoice_date' => $request->invoice_date,
                    'total_amount' => 0,
                ]);

                $totalAmount = 0;

                foreach ($request->items as $item) {

                    $product = Product::where('id', $item['product_id'])
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw ValidationException::withMessages([
                            'product' => 'Inactive product cannot be used'
                        ]);
                    }

                    $amount = round($item['weight'] * $item['rate'], 2);

                    /* ---------------------------
                    PURCHASE ITEM
                    ----------------------------*/
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['quantity'],
                        'weight'      => $item['weight'],
                        'rate'        => $item['rate'],
                        'amount'      => $amount,
                    ]);

                    /* ---------------------------
                    STOCK TABLE
                    ----------------------------*/
                    $stock = Stock::firstOrCreate(
                        ['product_id' => $item['product_id']],
                        ['quantity' => 0, 'weight' => 0]
                    );

                    $stock->quantity += $item['quantity'];
                    $stock->weight   += $item['weight'];
                    $stock->save();

                    /* ---------------------------
                    AVG PURCHASE RATE
                    (Weighted Average)
                    ----------------------------*/
                    $existingWeight = $product->opening_weight + ($stock->weight - $item['weight']);
                    $existingValue  = $existingWeight * $product->avg_purchase_rate;

                    $newValue = $existingValue + ($item['weight'] * $item['rate']);
                    $newWeight = $existingWeight + $item['weight'];

                    $product->avg_purchase_rate = $newWeight > 0
                        ? round($newValue / $newWeight, 2)
                        : 0;

                    $product->save();

                    /* ---------------------------
                    STOCK LEDGER
                    ----------------------------*/
                    // StockLedger::create([
                    //     'product_id'     => $item['product_id'],
                    //     'transaction_type' => 'purchase',
                    //     'reference_id'   => $purchase->id,
                    //     'weight_in'      => $item['weight'],
                    //     'weight_out'     => 0,
                    //     'balance'        => $stock->weight,
                    // ]);
                    StockLedger::create([
                        'product_id'     => $item['product_id'],
                        'type'           => 'purchase',        // ✅ REQUIRED
                        'reference_id'   => $purchase->id,
                        'qty_in'         => $item['quantity'] ?? 0,
                        'qty_out'        => 0,
                        'weight_in'      => $item['weight'],
                        'weight_out'     => 0,
                        'balance_qty'    => $stock->quantity,
                        'balance_weight' => $stock->weight,
                        'rate'           => $item['rate'],     // ✅ IMPORTANT for valuation
                    ]);
                    LedgerEntry::create([
                        'account_type' => 'party',
                        'account_id'   => $purchase->party_id,
                        'date'         => $purchase->invoice_date,
                        'voucher_type' => 'purchase',
                        'voucher_id'   => $purchase->id,
                        'debit'        => 0,
                        'credit'       => $purchase->total_amount,
                        'narration'    => 'Purchase Invoice #' . $purchase->invoice_no
                    ]);

                    $totalAmount += $amount;
                }

                $purchase->update([
                    'total_amount' => $totalAmount
                ]);
            });

            return response()->json(['message' => 'Purchase created successfully']);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Purchase save failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
