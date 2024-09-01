<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'account', 'amount', 'reason', 'out_date', 'is_borrowed', 'customer_id', 'status', 'amount_paid',
    ];
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
