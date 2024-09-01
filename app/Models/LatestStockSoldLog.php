<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatestStockSoldLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'stock_id', 'qty_in', 'qty_out', 'time_created',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
