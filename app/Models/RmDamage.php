<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmDamage extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'raw_material_id', 'quantity', 'unit_cost', 'reason',
    ];
}
