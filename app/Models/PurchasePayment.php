<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'purchase_id', 'trans_id', 'pay_date', 'amount', 'currency', 'defcurr', 'ex_rate', 'account', 'pv_no',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
