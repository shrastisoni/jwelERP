<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Support\Facades\DB;

class CustomerLedgerController extends Controller
{
    public function ledger($customerId)
    {
        $customer = Party::where('id', $customerId)
            ->where('type', 'customer')
            ->firstOrFail();

        $rows = [];

        // 🔹 Opening Balance
        if ($customer->opening_balance != 0) {
            $rows[] = [
                'date' => null,
                'particulars' => 'Opening Balance',
                'debit' => $customer->opening_balance > 0 ? $customer->opening_balance : 0,
                'credit' => $customer->opening_balance < 0 ? abs($customer->opening_balance) : 0,
            ];
        }

        // 🔹 Sales (Debit)
        $sales = DB::table('sales')
            ->where('party_id', $customerId)
            ->select('created_at as date', 'invoice_no', 'total_amount')
            ->get();

        foreach ($sales as $sale) {
            $rows[] = [
                'date' => $sale->date,
                'particulars' => 'Sale - ' . $sale->invoice_no,
                'debit' => $sale->total_amount,
                'credit' => 0
            ];
        }

        // 🔹 Payments (Credit)
        $payments = DB::table('payments')
            ->where('party_id', $customerId)
            ->where('type', 'in')
            ->select('created_at as date', 'amount')
            ->get();

        foreach ($payments as $pay) {
            $rows[] = [
                'date' => $pay->date,
                'particulars' => 'Payment Received',
                'debit' => 0,
                'credit' => $pay->amount
            ];
        }

        // 🔹 Sort by date
        usort($rows, function ($a, $b) {
            return strtotime($a['date'] ?? '1970-01-01')
                 <=> strtotime($b['date'] ?? '1970-01-01');
        });

        // 🔹 Running Balance
        $balance = 0;
        foreach ($rows as &$row) {
            $balance += $row['debit'] - $row['credit'];
            $row['balance'] = $balance;
        }

        return response()->json([
            'customer' => $customer,
            'ledger' => $rows,
            'closing_balance' => $balance
        ]);
    }
}
