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
use Illuminate\Validation\ValidationException;
class PurchaseController extends Controller
{
    public function index()
    {
        return Purchase::with('items.product','party')->latest()->get();
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
                    'party_id' => $request->party_id,
                    'invoice_no' => 'PUR-' . strtoupper(Str::random(8)),
                    'invoice_date' => $request->invoice_date,
                    'total_amount' => 0,
                ]);

                $total = 0;

                foreach ($request->items as $item) {

                    $amount = $item['weight'] * $item['rate'];
                    $product = Product::where('id', $item['product_id'])
                        ->where('is_active', true)
                        ->first();

                    if (!$product) {
                        throw ValidationException::withMessages([
                            'product' => 'Inactive product cannot be used'
                        ]);
                    }
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'weight' => $item['weight'],
                        'rate' => $item['rate'],
                        'amount' => $amount,
                    ]);

                    // STOCK UPDATE
                    $stock = Stock::firstOrCreate(
                        ['product_id' => $item['product_id']],
                        ['quantity' => 0, 'weight' => 0]
                    );

                    $stock->increment('quantity', $item['quantity']);
                    $stock->increment('weight', $item['weight']);

                    $total += $amount;
                    StockLedger::create([
                        'product_id'      => $item['product_id'],
                        'type'            => 'purchase',
                        'reference_id'    => $purchase->id,
                        'qty_in'          => $item['quantity'],
                        'qty_out'         => 0,
                        'weight_in'       => $item['weight'],
                        'weight_out'      => 0,
                        'balance_qty'     => $stock->quantity,
                        'balance_weight'  => $stock->weight,
                    ]);

                }

                $purchase->update(['total_amount' => $total]);
            });
        }catch (ValidationException $e) {
            
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
        return response()->json(['message' => 'Purchase created successfully']);
    }

    public function show($id)
    {
        return Purchase::with('items.product','party')->findOrFail($id);
    }
}
