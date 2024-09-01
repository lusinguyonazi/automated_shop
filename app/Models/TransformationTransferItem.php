<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransformationTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_order_id','shop_id', 'product_id', 'source_stock', 'destin_stock', 'quantity',
    ];

        public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
