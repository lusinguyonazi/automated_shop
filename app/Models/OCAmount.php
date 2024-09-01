<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OCAmount extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'record_type', 'amount_type', 'operator', 'date', 'amount',
    ];


    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
