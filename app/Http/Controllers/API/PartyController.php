<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
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
    $parties->each(function ($p) {
        if ($p->type === 'customer') {
            $p->outstanding =
                ($p->total_sales ?? 0) - ($p->received_amount ?? 0);
        } else {
            $p->outstanding =
                ($p->total_purchases ?? 0) - ($p->paid_amount ?? 0);
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
}
