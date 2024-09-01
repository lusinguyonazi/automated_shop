<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmPurchaseItemTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'packing_material_id', 'qty', 'unit_cost', 'total',
    ];

    public function packingMaterial()
    {
        return $this->belongsTo(PackingMaterial::class);
    }


}
