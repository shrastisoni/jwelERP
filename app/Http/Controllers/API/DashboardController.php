<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSales = DB::table('sales')->sum('total_amount');
        $totalPurchase = DB::table('purchases')->sum('total_amount');

        $profit = $totalSales - $totalPurchase;

        // Stock value from latest stock ledger
        $stockValue = DB::table('stock_ledgers as sl')
            ->selectRaw('SUM(sl.balance_weight * sl.rate) as value')
            ->whereIn('sl.id', function ($q) {
                $q->select(DB::raw('MAX(id)'))
                  ->from('stock_ledgers')
                  ->groupBy('product_id');
            })
            ->value('value');

        return response()->json([
            'total_sales'    => $totalSales ?? 0,
            'total_purchase' => $totalPurchase ?? 0,
            'profit'         => $profit ?? 0,
            'stock_value'    => $stockValue ?? 0,
        ]);
    }

    public function charts()
    {
        $sales = DB::table('sales')
            ->selectRaw("strftime('%m', created_at) as month, SUM(total_amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $profit = DB::table('sales')
            ->selectRaw("
                strftime('%m', sales.created_at) as month,
                SUM(sales.total_amount - purchases.total_amount) as profit
            ")
            ->leftJoin('purchases', DB::raw("strftime('%m', purchases.created_at)"), '=', DB::raw("strftime('%m', sales.created_at)"))
            ->groupBy('month')
            ->pluck('profit', 'month');

        return response()->json([
            'sales' => [
                'labels' => array_keys($sales->toArray()),
                'values' => array_values($sales->toArray()),
            ],
            'profit' => [
                'labels' => array_keys($profit->toArray()),
                'values' => array_values($profit->toArray()),
            ]
        ]);
    }
}
