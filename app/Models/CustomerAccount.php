<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAccount extends Model
{
    use HasFactory;

     protected $fillable = [
        'shop_id', 'customer_id', 'sale_no', 'amount', 'receipt_no', 'payment', 'payment_mode', 'bank_name', 'bank_breanch', 'cheque_no', 'expire_date', 'adjustment', 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
