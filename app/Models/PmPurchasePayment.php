<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmPurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pm_purchase_id', 'pay_date',   'amount', 'account', 'pv_no',
    ];

    public function purchase()
    {
        return $this->belongsTo(PmPurchase::class);
    }
}
