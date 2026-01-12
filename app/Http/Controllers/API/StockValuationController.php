<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;

class StockValuationController extends Controller
{
    public function index()
    {
        /*
         SQLite-safe query
         Gets last ledger entry per product
        */

        $rows = DB::table('stock_ledgers as sl')
            ->select(
                'sl.product_id',
                'p.name as product_name',
                'sl.balance_weight',
                'sl.rate',
                DB::raw('(sl.balance_weight * sl.rate) as value')
            )
            ->join('products as p', 'p.id', '=', 'sl.product_id')
            ->whereIn('sl.id', function ($q) {
                $q->select(DB::raw('MAX(id)'))
                  ->from('stock_ledgers')
                  ->groupBy('product_id');
            })
            ->get();

        $totalValue = $rows->sum('value');

        return response()->json([
            'items' => $rows,
            'total_value' => $totalValue
        ]);
    }
}
