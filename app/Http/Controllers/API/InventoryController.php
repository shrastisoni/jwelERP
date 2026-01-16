<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    // 🔹 Stock Summary
    public function index()
    {
        return DB::table('stocks as s')
            ->join('products as p', 'p.id', '=', 's.product_id')
            ->select(
                'p.id',
                'p.name',
                'p.metal',
                'p.purity',
                's.quantity',
                's.weight'
            )
            ->orderBy('p.name')
            ->get();
    }

    // 🔹 Product Ledger
    public function ledger($productId)
    {
        return DB::table('stock_ledgers')
            ->where('product_id', $productId)
            ->orderBy('id')
            ->get();
    }

    // 🔹 Category-wise stock
    public function categorySummary()
    {
        return DB::table('stocks as s')
            ->join('products as p', 'p.id', '=', 's.product_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->select(
                'c.name as category',
                DB::raw('SUM(s.quantity) as qty'),
                DB::raw('SUM(s.weight) as weight')
            )
            ->groupBy('c.id', 'c.name')
            ->get();
    }

    public function lowStock()
{
    return DB::table('stocks as s')
        ->join('products as p', 'p.id', '=', 's.product_id')
        ->select(
            'p.id',
            'p.name',
            'p.metal',
            'p.purity',
            's.weight',
            'p.min_stock'
        )
        ->whereColumn('s.weight', '<=', 'p.min_stock')
        ->orderBy('s.weight')
        ->get();
}

}
