<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'reference_id',
        'qty_in',
        'qty_out',
        'weight_in',
        'weight_out',
        'balance_qty',
        'balance_weight',
        'rate'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
