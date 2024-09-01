<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmUseItemTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'packing_material_id', 'unit_cost', 'total',
    ];
}
