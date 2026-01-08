<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        // return response()->json($request->search);
        $q = Party::query()->orderBy('name'); 

        if ($request->filled('type')) {
            $q->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $q->where(function ($x) use ($request) {
                $x->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json($q->get());
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
