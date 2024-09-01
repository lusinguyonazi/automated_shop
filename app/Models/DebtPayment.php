<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'an_sale_id', 'pay_mode', 'pay_date',  'amount',
    ];

    public function sale()
    {
        return $this->belongsTo(AnSale::class);
    }
}
