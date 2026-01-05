<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Party;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->type) 
        {
            $query = Party::query();
            $query->where('type', $request->type);
            return $query->get();
        }else{ 
            return Party::all();
        }
       
    }
    
    // public function getPartyRecord(Request $request)
    // {
    //     $query = Party::query();

    //     if ($request->type) {
    //         $query->where('type', $request->type);
    //     }
    //     return $query->get();
    // }

}
