<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItemTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'quantity_sold', 'buying_per_unit', 'buying_price', 'price_per_unit', 'discount', 'price', 'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(AnSale::class);
    }
}
