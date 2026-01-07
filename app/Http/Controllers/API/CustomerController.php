<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;

        return Party::where('type', 'customer')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('mobile', 'like', "%$q%");
                });
            })
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'mobile' => 'nullable'
        ]);

        Party::create([
            'name'    => $request->name,
            'type'    => 'customer',
            'mobile'  => $request->mobile,
            'email'   => $request->email,
            'address' => $request->address
        ]);

        return response()->json(['message' => 'Customer added']);
    }

    public function update(Request $request, Party $customer)
    {
        if ($customer->type !== 'customer') {
            return response()->json(['message' => 'Invalid customer'], 403);
        }

        $request->validate([
            'name' => 'required'
        ]);

        $customer->update([
            'name'    => $request->name,
            'mobile'  => $request->mobile,
            'email'   => $request->email,
            'address' => $request->address
        ]);

        return response()->json(['message' => 'Customer updated']);
    }

    public function destroy(Party $customer)
    {
        if ($customer->type !== 'customer') {
            return response()->json(['message' => 'Invalid customer'], 403);
        }

        // OPTIONAL: prevent delete if sales exist
        // if ($customer->sales()->exists()) { ... }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted']);
    }
}
