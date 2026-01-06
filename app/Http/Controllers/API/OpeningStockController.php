<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OpeningStockController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'weight'     => 'required|numeric|min:0.001',
            'rate'       => 'required|numeric|min:0',
        ]);

        // 🔒 Prevent duplicate opening stock
        $exists = StockLedger::where('product_id', $data['product_id'])
            ->where('type', 'opening')
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'opening' => 'Opening stock already entered for this product'
            ]);
        }

        // 🔢 Current balance
        $lastBalance = StockLedger::where('product_id', $data['product_id'])
            ->orderByDesc('id')
            ->value('balance') ?? 0;

        $newBalance = $lastBalance + $data['weight'];

        StockLedger::create([
            'product_id'       => $data['product_id'],
            'type' => 'opening',
            'weight_in'        => $data['weight'],
            'weight_out'       => 0,
            'balance'          => $newBalance,
            'rate'             => $data['rate'],
        ]); 

        return response()->json([
            'message' => 'Opening stock added successfully'
        ]);
    }
}
