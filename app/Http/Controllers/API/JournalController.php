<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;

use Illuminate\Http\Request;
class JournalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'entries' => 'required|array|min:2'
        ]);

        $totalDr = collect($request->entries)->sum('debit');
        $totalCr = collect($request->entries)->sum('credit');

        if ($totalDr != $totalCr) {
            return response()->json(['message' => 'Debit and Credit must match'], 422);
        }

        foreach ($request->entries as $e) {
            LedgerEntry::create($e + [
                'voucher_type' => 'journal'
            ]);
        }

        return response()->json(['message' => 'Journal posted']);
    }
}
