<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmUse extends Model
{
    use HasFactory;

     protected $fillable = [
        'shop_id', 'user_id', 'total_cost', 'date' ,'prod_batch',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function rmusedItems()
    {
        return $this->hasMany(RmUsedItem::class);
    }
}
