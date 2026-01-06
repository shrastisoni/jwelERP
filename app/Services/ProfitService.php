<?php

namespace App\Services;

use App\Models\PurchaseItem;
use App\Models\SaleItem;

class ProfitService
{
    public static function productProfit($productId)
    {
        // TOTAL PURCHASE
        $purchase = PurchaseItem::where('product_id', $productId)
            ->selectRaw('SUM(weight) as qty, SUM(weight * rate) as amount')
            ->first();

        if (!$purchase || $purchase->qty == 0) {
            return 0;
        }

        $avgRate = $purchase->amount / $purchase->qty;

        // TOTAL SALE
        $sale = SaleItem::where('product_id', $productId)
            ->selectRaw('SUM(weight) as qty, SUM(weight * rate) as amount')
            ->first();

        if (!$sale || $sale->qty == 0) {
            return 0;
        }

        $cost = $sale->qty * $avgRate;

        return round($sale->amount - $cost, 2);
    }
    public static function calculateProductProfit(int $productId): array
    {
        // 1️⃣ TOTAL PURCHASE COST
        $purchase = PurchaseItem::where('product_id', $productId)
            ->selectRaw('
                SUM(weight) as total_weight,
                SUM(weight * rate) as total_amount
            ')
            ->first();

        if (!$purchase || $purchase->total_weight <= 0) {
            return [
                'purchase_cost' => 0,
                'sale_amount'   => 0,
                'profit'        => 0
            ];
        }

        $avgCost = $purchase->total_amount / $purchase->total_weight;

        // 2️⃣ TOTAL SALES
        $sale = SaleItem::where('product_id', $productId)
            ->selectRaw('
                SUM(weight) as sold_weight,
                SUM(weight * rate) as sale_amount
            ')
            ->first();

        if (!$sale || $sale->sold_weight <= 0) {
            return [
                'purchase_cost' => 0,
                'sale_amount'   => 0,
                'profit'        => 0
            ];
        }

        // 3️⃣ COST OF SOLD GOODS
        $costOfSale = $sale->sold_weight * $avgCost;

        // 4️⃣ FINAL PROFIT
        $profit = $sale->sale_amount - $costOfSale;

        return [
            'purchase_cost' => round($costOfSale, 2),
            'sale_amount'   => round($sale->sale_amount, 2),
            'profit'        => round($profit, 2)
        ];
    }
}
