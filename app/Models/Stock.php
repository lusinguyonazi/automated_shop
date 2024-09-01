<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'product_id', 'quantity_in', 'buying_per_unit', 'source', 'expire_date', 'order_id', 'time_created',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }
}
