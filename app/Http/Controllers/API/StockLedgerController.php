<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StockLedger;

class StockLedgerController extends Controller
{
    public function index()
    {
        return StockLedger::with('product')
            ->latest()
            ->get();
    }

    public function productLedger($productId)
    {
        return StockLedger::with('product')
            ->where('product_id', $productId)
            ->orderBy('id')
            ->get();
    }
}
