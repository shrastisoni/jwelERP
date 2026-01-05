<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            }

            $purchase->update(['total_amount' => $total]);
        });

        return response()->json(['message' => 'Purchase created successfully']);
    }

    public function show($id)
    {
        return Purchase::with('items.product','party')->findOrFail($id);
    }
}
