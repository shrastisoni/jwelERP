<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\StockService;
use DB;

class SaleControllerOld extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'party_id' => 'required|exists:parties,id',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.weight' => 'required|numeric|min:0.01',
        'items.*.rate' => 'required|numeric|min:0.01'
    ]);

    DB::transaction(function () use ($request) {

        $sale = Sale::create([
            'party_id' => $request->party_id,
            'invoice_no' => uniqid('INV'),
            'total_amount' => $request->total_amount
        ]);

        foreach ($request->items as $item) {

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'weight' => $item['weight'],
                'rate' => $item['rate'],
                'amount' => $item['weight'] * $item['rate']
            ]);

            StockService::deduct(
                $item['product_id'],
                $item['weight'],
                1
            );
        }
    });

    return response()->json(['message' => 'Sale created']);
}

    // public function store(Request $request)
    // {
    //     DB::transaction(function () use ($request) {

    //         $sale = Sale::create([
    //             'party_id' => $request->party_id,
    //             'invoice_no' => uniqid('INV'),
    //             'total_amount' => $request->total_amount
    //         ]);

    //         foreach ($request->items as $item) {

    //             SaleItem::create([
    //                 'sale_id' => $sale->id,
    //                 'product_id' => $item['product_id'],
    //                 'weight' => $item['weight'],
    //                 'rate' => $item['rate'],
    //                 'amount' => $item['amount']
    //             ]);

    //             StockService::deduct(
    //                 $item['product_id'],
    //                 $item['weight'],
    //                 1
    //             );
    //         }
    //     });

    //     return response()->json(['message' => 'Sale saved']);
    // }
}
