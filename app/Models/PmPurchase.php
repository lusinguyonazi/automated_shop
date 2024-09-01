<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'supplier_id', 'grn_no', 'order_no', 'delivery_note_no', 'invoice_no', 'total_amount', 'amount_paid', 'date', 'purchase_type',
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
        return $this->hasMany(PmPurchasePayment::class);
    }
}
