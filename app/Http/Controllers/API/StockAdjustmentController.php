<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric',
            'weight'     => 'required|numeric',
            'reason'     => 'required'
        ]);

        DB::transaction(function () use ($request) {

            $stock = Stock::firstOrCreate(
                ['product_id' => $request->product_id],
                ['quantity' => 0, 'weight' => 0]
            );

            $stock->quantity += $request->quantity;
            $stock->weight += $request->weight;
            $stock->save();

            StockLedger::create([
                'product_id' => $request->product_id,
                'type' => 'adjustment',
                'reference_id' => null,
                'qty_in' => max($request->quantity, 0),
                'qty_out' => abs(min($request->quantity, 0)),
                'weight_in' => max($request->weight, 0),
                'weight_out' => abs(min($request->weight, 0)),
                'balance_qty' => $stock->quantity,
                'balance_weight' => $stock->weight,
                'rate' => 0
            ]);
        });

        return response()->json(['message' => 'Stock adjusted']);
    }
}
