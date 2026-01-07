<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'party_id',
        'invoice_no',
        'invoice_date',
        'total_amount'
    ];

    public function items() {
        return $this->hasMany(SaleItem::class);
    }

    public function party() {
        return $this->belongsTo(Party::class);
    }
}
