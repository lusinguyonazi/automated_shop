<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'trans_id', 'cash_out_id', 'account', 'amount', 'source', 'in_date',
    ];
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
