<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoryValuationController extends Controller
{
    public function index()
    {
        // Latest ledger per product
        $latestLedger = DB::table('stock_ledgers')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('product_id');

        $rows = DB::table('stock_ledgers as sl')
            ->joinSub($latestLedger, 'latest', function ($join) {
                $join->on('sl.id', '=', 'latest.id');
            })
            ->join('products as p', 'p.id', '=', 'sl.product_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->select(
                'c.id as category_id',
                'c.name as category_name',
                DB::raw('SUM(sl.balance_weight) as total_weight'),
                DB::raw('SUM(sl.balance_weight * sl.rate) as total_value')
            )
            ->groupBy('c.id', 'c.name')
            ->orderBy('c.name')
            ->get();

        return response()->json($rows);
    }
}
