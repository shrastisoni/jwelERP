<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpeningStockController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'weight'     => 'required|numeric|min:0.001',
            'rate'       => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {

            if (Stock::where('product_id', $request->product_id)->exists()) {
                throw ValidationException::withMessages([
                    'product' => 'Opening stock already exists for this product'
                ]);
            }

            $stock = Stock::create([
                'product_id' => $request->product_id,
                'quantity'   => 0,
                'weight'     => $request->weight,
            ]);

            StockLedger::create([
                'product_id'     => $request->product_id,
                'type'           => 'opening',
                'reference_id'   => null,
                'qty_in'         => 0,
                'qty_out'        => 0,
                'weight_in'      => $request->weight,
                'weight_out'     => 0,
                'balance_qty'    => 0,
                'balance_weight' => $request->weight,
                'rate'           => $request->rate,
            ]);
        });

        return response()->json([
            'message' => 'Opening stock added successfully'
        ]);
    }
}
