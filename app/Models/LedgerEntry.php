<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LedgerEntry extends Model
{
    protected $fillable = [
        'account_type',
        'account_id',
        'date',
        'voucher_type',
        'voucher_id',
        'debit',
        'credit',
        'narration'
    ];
}
