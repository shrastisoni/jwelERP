<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
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
    
    public function index(Request $request)
    {
        $q = Payment::with('party')->orderByDesc('id');

        if ($request->search) {
            $q->whereHas('party', function ($p) use ($request) {
                $p->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->type) {
            $q->where('type', $request->type);
        }

        return response()->json(
            $q->paginate(20)
        );
    }

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
