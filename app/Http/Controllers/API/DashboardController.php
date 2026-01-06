<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\StockLedger;

    use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function index()
    {
        // Total products (active)
        $totalProducts = Product::where('is_active', true)->count();

        // Total stock weight
        $totalStock = StockLedger::selectRaw('SUM(weight_in - weight_out) as stock')
            ->value('stock') ?? 0;

        // Total purchase amount
        $totalPurchase = PurchaseItem::selectRaw('SUM(weight * rate) as total')
            ->value('total') ?? 0;

        // Total sales amount
        $totalSales = SaleItem::selectRaw('SUM(weight * rate) as total')
            ->value('total') ?? 0;

        // Profit (purchase cost based)
        $profit = $totalSales - $totalPurchase;

        // Low stock products (example < 5 gm)
        $lowStock = StockLedger::select('product_id')
            ->selectRaw('SUM(weight_in - weight_out) as balance')
            ->groupBy('product_id')
            ->having('balance', '<', 5)
            ->with('product:id,name')
            ->get();

        return response()->json([
            'total_products' => $totalProducts,
            'total_stock'    => round($totalStock, 3),
            'total_purchase' => round($totalPurchase, 2),
            'total_sales'    => round($totalSales, 2),
            'profit'         => round($profit, 2),
            'low_stock'      => $lowStock
        ]);
    }

    public function charts()
    {
        // Monthly Sales
        $sales = DB::table('sale_items')
            ->selectRaw('
                strftime("%Y-%m", created_at) as month,
                SUM(weight * rate) as total
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Monthly Purchase
        $purchase = DB::table('purchase_items')
            ->selectRaw('
                strftime("%Y-%m", created_at) as month,
                SUM(weight * rate) as total
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Convert to arrays
        $months = [];
        $salesData = [];
        $profitData = [];

        foreach ($sales as $s) {
            $months[] = $s->month;

            $saleAmount = $s->total;

            $purchaseAmount = $purchase
                ->firstWhere('month', $s->month)
                ->total ?? 0;

            $salesData[] = round($saleAmount, 2);
            $profitData[] = round($saleAmount - $purchaseAmount, 2);
        }

        return response()->json([
            'months' => $months,
            'sales'  => $salesData,
            'profit' => $profitData
        ]);
    }

}
