<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdDamage extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'product_id', 'quantity', 'reason', 'time_created', 'deph_measure', 'in_stock', 'curr_price',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
