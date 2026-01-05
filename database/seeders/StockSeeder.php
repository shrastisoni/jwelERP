<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\PurchaseItem;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing stock (safe for dev)
        Stock::truncate();

        // Group purchase items by product
        $items = PurchaseItem::selectRaw('
                product_id,
                SUM(quantity) as total_qty,
                SUM(weight) as total_weight
            ')
            ->groupBy('product_id')
            ->get();

        foreach ($items as $item) {
            Stock::create([
                'product_id' => $item->product_id,
                'quantity'   => $item->total_qty,
                'weight'     => $item->total_weight,
            ]);
        }
    }
}
