<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'an_sale_id', 'quantity_sold', 'buying_per_unit', 'buying_price', 'price_per_unit', 'discount', 'price', 'product_id', 'shop_id', 'time_created', 'sync_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function anSale()
    {
        return $this->belongsTo(AnSale::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
