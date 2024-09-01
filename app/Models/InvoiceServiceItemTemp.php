<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceServiceItemTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'shop_id', 'service_id', 'repeatition', 'cost_per_unit',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
