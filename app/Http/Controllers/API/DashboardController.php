<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->summary();
    }

    /* ---------------------------------
       STOCK VALUE (CORRECT)
    ---------------------------------*/
    public function stockValue()
    {
        $value = DB::table('stock_ledgers')
            ->where('type', 'purchase')
            ->selectRaw('
                SUM(balance_weight * rate) as value
            ')
            ->first();

        return response()->json([
            'stock_value' => round($value->value ?? 0, 2)
        ]);
    }

    /* ---------------------------------
       TOTAL SALES
    ---------------------------------*/
    public function totalSales()
    {
        $total = DB::table('sales')
            ->selectRaw('SUM(total_amount) as total')
            ->first();

        return response()->json([
            'total_sales' => round($total->total ?? 0, 2)
        ]);
    }

    /* ---------------------------------
       TOTAL PROFIT (SALE − PURCHASE)
    ---------------------------------*/
    public function totalProfit()
    {
        $profit = DB::table('sale_items as si')
            ->join('stock_ledgers as sl', function ($join) {
                $join->on('sl.product_id', '=', 'si.product_id')
                     ->where('sl.type', '=', 'purchase');
            })
            ->selectRaw('
                SUM(
                    (si.weight * si.rate)
                    - (si.weight * sl.rate)
                ) as profit
            ')
            ->first();

        return response()->json([
            'profit' => round($profit->profit ?? 0, 2)
        ]);
    }

    /* ---------------------------------
       DASHBOARD SUMMARY
    ---------------------------------*/
    public function summary()
    {
        $stock = DB::table('stock_ledgers')
            ->where('type', 'purchase')
            ->selectRaw('SUM(balance_weight * rate) as value')
            ->first();

        $sales = DB::table('sales')
            ->selectRaw('SUM(total_amount) as total')
            ->first();

        $profit = DB::table('sale_items as si')
            ->join('stock_ledgers as sl', function ($join) {
                $join->on('sl.product_id', '=', 'si.product_id')
                     ->where('sl.type', '=', 'purchase');
            })
            ->selectRaw('
                SUM(
                    (si.weight * si.rate)
                    - (si.weight * sl.rate)
                ) as profit
            ')
            ->first();

        return response()->json([
            'stock_value' => round($stock->value ?? 0, 2),
            'total_sales' => round($sales->total ?? 0, 2),
            'profit'      => round($profit->profit ?? 0, 2),
        ]);
    }
    public function recentSales()
    {
        return DB::table('sales')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }
    public function lowStock()
    {
        return DB::table('stock_ledgers as sl')
            ->join('products as p', 'p.id', '=', 'sl.product_id')
            ->where('sl.balance_weight', '<', 5)
            ->select('p.name', 'sl.balance_weight')
            ->get();
    }

    public function charts()
    {
        $sales = DB::table('sales')
            ->selectRaw("strftime('%m', created_at) as month, SUM(total_amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $profit = DB::table('sale_items')
            ->selectRaw("strftime('%m', created_at) as month, SUM((weight * rate)) as profit")
            ->groupBy('month')
            ->pluck('profit', 'month');

        return response()->json([
            'sales' => [
                'labels' => $sales->keys(),
                'values' => $sales->values()
            ],
            'profit' => [
                'labels' => $profit->keys(),
                'values' => $profit->values()
            ]
        ]);
    }


}
