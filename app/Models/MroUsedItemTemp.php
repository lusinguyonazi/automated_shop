<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MroUsedItemTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'mro_id', 'unit_cost', 'total', 'date'
    ];
}
