<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    // Party Ledger
    public function partyLedger($partyId)
    {
        return LedgerEntry::where('account_type', 'party')
            ->where('account_id', $partyId)
            ->orderBy('date')
            ->get();
    }

    // Outstanding
    public function outstanding()
    {
        return LedgerEntry::selectRaw('
                account_id,
                SUM(debit - credit) as balance
            ')
            ->where('account_type', 'party')
            ->groupBy('account_id')
            ->having('balance', '!=', 0)
            ->get();
    }

    // Day Book
    public function dayBook(Request $request)
    {
        return LedgerEntry::whereDate('date', $request->date ?? now())
            ->orderBy('id')
            ->get();
    }
}

