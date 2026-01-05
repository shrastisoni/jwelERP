<?php

namespace App\Http\Controllers\API;

use App\Models\StockLedger;
use App\Http\Controllers\Controller;

class StockLedgerController extends Controller
{
    public function index()
    {
        return StockLedger::with('product')
            ->latest()
            ->get();
    }
}

