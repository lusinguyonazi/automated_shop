<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id', 'an_sale_id',
    ];
}
