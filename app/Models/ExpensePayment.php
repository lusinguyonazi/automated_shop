<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'expense_id', 'pay_date',   'amount', 'account', 'pv_no',
    ];

    public function expense()
    {
        return $this->belongsTo(AnCost::class);
    }
}
