<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Purchase;
class PartyController extends Controller
{
    // public function index(Request $request)
    // {
    //     // return response()->json($request->search);
    //     $q = Party::query()->orderBy('name'); 

    //     if ($request->filled('type')) {
    //         $q->where('type', $request->type);
    //     }

    //     if ($request->filled('search')) {
    //         $q->where(function ($x) use ($request) {
    //             $x->where('name', 'like', '%' . $request->search . '%')
    //               ->orWhere('mobile', 'like', '%' . $request->search . '%');
    //         });
    //     }

    //     return response()->json($q->get());
    // }
    

public function index(Request $request)
{
    $q = Party::query()
        ->select('parties.*')

        // CUSTOMER OUTSTANDING
        ->selectSub(function ($q) {
            $q->from('sales')
              ->selectRaw('IFNULL(SUM(total_amount),0)')
              ->whereColumn('sales.party_id', 'parties.id');
        }, 'total_sales')

        ->selectSub(function ($q) {
            $q->from('payments')
              ->selectRaw('IFNULL(SUM(amount),0)')
              ->whereColumn('payments.party_id', 'parties.id')
              ->where('payments.type', 'receive');
        }, 'received_amount')

        // SUPPLIER OUTSTANDING
        ->selectSub(function ($q) {
            $q->from('purchases')
              ->selectRaw('IFNULL(SUM(total_amount),0)')
              ->whereColumn('purchases.party_id', 'parties.id');
        }, 'total_purchases')

        ->selectSub(function ($q) {
            $q->from('payments')
              ->selectRaw('IFNULL(SUM(amount),0)')
              ->whereColumn('payments.party_id', 'parties.id')
              ->where('payments.type', 'pay');
        }, 'paid_amount');

    // SEARCH
    if ($request->search) {
        $q->where(function ($x) use ($request) {
            $x->where('name', 'like', "%{$request->search}%")
              ->orWhere('mobile', 'like', "%{$request->search}%");
        });
    }

    // FILTER TYPE
    if ($request->type) {
        $q->where('type', $request->type);
    }

    $parties = $q->orderBy('name')->get();

    // CALCULATE OUTSTANDING
    // $parties->each(function ($p) {
    //     if ($p->type === 'customer') {
    //         $p->outstanding =
    //             ($p->total_sales ?? 0) - ($p->received_amount ?? 0);
    //     } else {
    //         $p->outstanding =
    //             ($p->total_purchases ?? 0) - ($p->paid_amount ?? 0);
    //     }
    // });
    $parties->each(function ($p) {

    $opening = $p->opening_type === 'debit'
            ? $p->opening_balance
            : -$p->opening_balance;

        if ($p->type === 'customer') {
            $p->outstanding =
                $opening
                + ($p->total_sales ?? 0)
                - ($p->received_amount ?? 0);
        } else {
            $p->outstanding =
                $opening
                + ($p->total_purchases ?? 0)
                - ($p->paid_amount ?? 0);
        }
    });


    return response()->json($parties);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string',
            'type'    => 'required|in:customer,supplier',
            'mobile'  => 'nullable|string',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_type' => 'required|in:debit,credit',
        ]);

        return response()->json(
            Party::create($data),
            201
        );
    }

    public function update(Request $request, $id)
    {
        $party = Party::findOrFail($id);

        $party->update(
            $request->validate([
                'name'    => 'required|string',
                'type'    => 'required|in:customer,supplier',
                'mobile'  => 'nullable|string',
                'address' => 'nullable|string',
                'opening_balance' => 'nullable|numeric|min:0',
                'opening_type' => 'required|in:debit,credit',
            ])
        );

        return response()->json($party);
    }

    public function destroy($id)
    {
        $party = Party::findOrFail($id);

        // 🔒 ERP SAFETY
        if (
            $party->payments()->exists() ||
            $party->sales()->exists() ||
            $party->purchases()->exists()
        ) {
            throw ValidationException::withMessages([
                'party' => 'Party has transactions and cannot be deleted'
            ]);
        }

        $party->delete();

        return response()->json(['message' => 'Party deleted']);
    }

    public function ledger($id)
{
    $party = Party::findOrFail($id);

    $rows = collect();
    $rows = collect();

// OPENING BALANCE
if ($party->opening_balance > 0) {

    if ($party->opening_type === 'debit') {
        $rows->push([
            'date' => null,
            'particular' => 'Opening Balance',
            'debit' => $party->opening_balance,
            'credit' => 0,
        ]);
    } else {
        $rows->push([
            'date' => null,
            'particular' => 'Opening Balance',
            'debit' => 0,
            'credit' => $party->opening_balance,
        ]);
    }
}

    // SALES (CUSTOMER)
    if ($party->type === 'customer') {
        Sale::where('party_id', $id)->each(function ($s) use ($rows) {
            $rows->push([
                'date' => $s->invoice_date,
                'particular' => 'Sale - ' . $s->invoice_no,
                'debit' => $s->total_amount,
                'credit' => 0,
            ]);
        });
    }

    // PURCHASES (SUPPLIER)
    if ($party->type === 'supplier') {
        Purchase::where('party_id', $id)->each(function ($p) use ($rows) {
            $rows->push([
                'date' => $p->invoice_date,
                'particular' => 'Purchase - ' . $p->invoice_no,
                'debit' => 0,
                'credit' => $p->total_amount,
            ]);
        });
    }

    // PAYMENTS
    Payment::where('party_id', $id)->each(function ($pay) use ($rows, $party) {

        if ($party->type === 'customer') {
            $rows->push([
                'date' => $pay->date,
                'particular' => 'Payment Received',
                'debit' => 0,
                'credit' => $pay->amount,
            ]);
        } else {
            $rows->push([
                'date' => $pay->date,
                'particular' => 'Payment Paid',
                'debit' => $pay->amount,
                'credit' => 0,
            ]);
        }
    });

    $ledger = $rows->sortBy('date')->values();

    return response()->json([
        'party' => $party,
        'ledger' => $ledger
    ]);
}

}
