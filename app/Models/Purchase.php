<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'party_id',
        'invoice_no',
        'invoice_date',
        'total_amount',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
