<?php
namespace App\Services;

use App\Models\Stock;
use App\Models\StockLedger;

class StockService
{
    public static function deduct($productId, $weight, $qty = 1)
    {
        $stock = Stock::firstOrCreate(
            ['product_id' => $productId],
            ['weight' => 0, 'quantity' => 0]
        );

        if ($stock->weight < $weight) {
            abort(400, 'Insufficient Stock');
        }

        $stock->weight -= $weight;
        $stock->quantity -= $qty;
        $stock->save();

        StockLedger::create([
            'product_id' => $productId,
            'type' => 'sale',
            'weight' => -$weight,
            'quantity' => -$qty,
            'balance_weight' => $stock->weight,
            'balance_qty' => $stock->quantity
        ]);
    }
}
