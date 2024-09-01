<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'an_sale_id', 'price', 'discount', 'service_id', 'time_created',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function anSale()
    {
        return $this->belongsTo(AnSale::class);
    }

}
