<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'tax_rate', 'business_account', 'inv_no_type',
    ];

    

    public function shop()
    {
        $this->belongsTo(Shop::class);
    }
}
