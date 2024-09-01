<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'an_sale_id', 'shop_id', 'trans_id', 'receipt_no', 'pay_date', 'amount', 'currency', 'defcurr', 'ex_rate', 'comments', 'pay_mode', 'bank_name', 'bank_branch', 'cheque_no',
    ];

    public function sale()
    {
        return $this->belongsTo(AnSale::class);
    }
}
