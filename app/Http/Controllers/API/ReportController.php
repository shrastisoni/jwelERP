<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function categoryStock()
    {
        $data = DB::table('categories')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->leftJoin('stock_ledgers', 'products.id', '=', 'stock_ledgers.product_id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('IFNULL(SUM(stock_ledgers.weight_in - stock_ledgers.weight_out),0) as total_stock')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        return response()->json($data);
    }
}
