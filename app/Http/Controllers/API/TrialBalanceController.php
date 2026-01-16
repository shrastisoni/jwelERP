<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
class TrialBalanceController extends Controller
{
    public function index()
    {
        return LedgerEntry::selectRaw('
                account_type,
                account_id,
                SUM(debit) as debit,
                SUM(credit) as credit
            ')
            ->groupBy('account_type', 'account_id')
            ->get();
    }
}
