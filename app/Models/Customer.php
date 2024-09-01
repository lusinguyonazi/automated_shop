<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'cust_id', 'name', 'phone', 'email', 'postal_address', 'physical_address', 'street', 'tin', 'vrn', 'country_code', 'time_created', 'cust_id_type', 'custid',
    ];

    public function sales()
    {
        return $this->hasMany(AnSale::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }


    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }
}
