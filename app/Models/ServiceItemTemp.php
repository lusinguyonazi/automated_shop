<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceItemTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'price', 'discount', 'total', 'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function sale()
    {
        return $this->belongsTo(AnSale::class);
    }
}
