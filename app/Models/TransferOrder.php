<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferOrder extends Model
{
    use HasFactory;

    protected $fillable = [
         'order_no', 'order_date', 'reason', 'user_id', 'shop_id', 'destination_id', 'source_product_id', 'source_product_quantity', 'is_transfomation_transfer',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function orderItems()
    {
        return $this->hasMany(TransferOrderItem::class);
    }
}
