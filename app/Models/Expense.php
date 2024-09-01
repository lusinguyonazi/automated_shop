<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'expense_category_id', 'expense_type', 'amount', 'no_days', 'amount_paid', 'account', 'exp_vat', 'wht_rate', 'wht_amount', 'time_created', 'description', 'supplier_id', 'order_no', 'pv_no', 'invoice_no', 'exp_type', 'status', 'trans_id', 'category_id',
    ];
    
    public function shop()
    {
        return $this->belongsTo(shop::class);
    }
}
