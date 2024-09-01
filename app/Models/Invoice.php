<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'inv_no', 'an_sale_id', 'shop_id',  'user_id' , 'business_account', 'due_date', 'status', 'note', 'vehicle_no',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
