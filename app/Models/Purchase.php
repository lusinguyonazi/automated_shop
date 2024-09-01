<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'supplier_id', 'grn_no', 'order_no', 'delivery_note_no', 'invoice_no', 'total_amount', 'amount_paid', 'currency', 'defcurr', 'ex_rate', 'time_created', 'purchase_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}
