<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StockLedger;
use Illuminate\Http\Request;
class StockLedgerController extends Controller
{
    // public function index()
    // {
    //     return StockLedger::with('product')
    //         ->latest()
    //         ->get();
    // }
    public function index(Request $request)
{
    $q = StockLedger::with('product');

    if ($request->product_id) {
        $q->where('product_id', $request->product_id);
    }

    if ($request->from_date) {
        $q->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->to_date) {
        $q->whereDate('created_at', '<=', $request->to_date);
    }

    return $q->orderBy('created_at')->get();
}

    public function productLedger($productId)
    {
        return StockLedger::with('product')
            ->where('product_id', $productId)
            ->orderBy('id')
            ->get();
    }
}
