<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmPurchaseItemTemp extends Model
{
    use HasFactory;

     protected $fillable = [
        'shop_id', 'raw_material_id', 'qty', 'unit_cost', 'total',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
