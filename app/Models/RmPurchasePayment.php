<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmPurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rm_purchase_id', 'pay_date',   'amount', 'account', 'pv_no',
    ];

    public function purchase()
    {
        return $this->belongsTo(RmPurchase::class);
    }
}
