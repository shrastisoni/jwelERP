<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'party_id',
        'date',  
        'amount',
        'type',
        'mode',
        'reference',
        'note'
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
