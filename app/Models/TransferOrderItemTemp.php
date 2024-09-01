<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferOrderItemTemp extends Model
{
    use HasFactory;

    protected $fillable = [
         'user_id', 'shop_id', 'product_id', 'source_stock', 'destin_stock', 'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
