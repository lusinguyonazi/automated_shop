<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_return_id', 'quantity', 'buying_per_unit', 'price_per_unit', 'price', 'discount', 'product_id', 'shop_id',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
