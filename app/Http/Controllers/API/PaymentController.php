<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
     public function index(Request $request)
    {
        $q = Payment::with('party')->orderByDesc('id');

        // 🔍 Party name search
        if ($request->filled('search')) {
            $q->whereHas('party', function ($p) use ($request) {
                $p->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Receive / Pay
        if ($request->filled('type')) {
            $q->where('type', $request->type);
        }

        // Date filters (optional but Munim-style)
        // if ($request->filled('from_date')) {
        //     $q->whereDate('created_at', '>=', $request->from_date);
        // }

        // if ($request->filled('to_date')) {
        //     $q->whereDate('created_at', '<=', $request->to_date);
        // }

        return response()->json(
            $q->paginate(20)
        );
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'party_id' => 'required|exists:parties,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:in,out',
            'mode' => 'nullable|string',
            'reference' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $party = Party::find($data['party_id']);

        // 🔒 Safety rule
        if ($data['type'] === 'in' && $party->type !== 'customer') {
            throw ValidationException::withMessages([
                'party' => 'Payment IN allowed only for customers'
            ]);
        }

        if ($data['type'] === 'out' && $party->type !== 'supplier') {
            throw ValidationException::withMessages([
                'party' => 'Payment OUT allowed only for suppliers'
            ]);
        }

        $payment = Payment::create($data);

        return response()->json($payment, 201);
    }

    // public function index(Request $request)
    // {
    //     return Payment::with('party')
    //         ->orderByDesc('id')
    //         ->paginate(20);
    // }
    
    // public function index(Request $request)
    // {
    //     $q = Payment::with('party')->orderByDesc('id');

    //     if ($request->search) {
    //         $q->whereHas('party', function ($p) use ($request) {
    //             $p->where('name', 'like', '%' . $request->search . '%');
    //         });
    //     }

    //     if ($request->type) {
    //         $q->where('type', $request->type);
    //     }

    //     return response()->json(
    //         $q->paginate(20)
    //     );
    // }

   

    // public function index(Request $request){
    //     $q = Payment::with('party');

    //     // 🔍 Party search
    //     if ($request->search) {
    //         $q->whereHas('party', function ($qr) use ($request) {
    //             $qr->where('name', 'like', '%' . $request->search . '%');
    //         });
    //     }

    //     // Filter by type (customer/supplier)
    //     if ($request->type) {
    //         $q->where('type', $request->type);
    //     }

    //     // Date filters
    //     if ($request->from_date) {
    //         $q->whereDate('created_at', '>=', $request->from_date);
    //     }

    //     if ($request->to_date) {
    //         $q->whereDate('created_at', '<=', $request->to_date);
    //     }

    //     return $q->orderBy('created_at', 'desc')->get();
    // }
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'mode'   => 'nullable|string',
            'note'   => 'nullable|string'
        ]);

        $payment->update($data);

        return response()->json($payment);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted'
        ]);
    }
}
