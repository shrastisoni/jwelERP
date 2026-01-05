<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'weight',
        'quantity',
        'balance_weight',
        'balance_qty'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
