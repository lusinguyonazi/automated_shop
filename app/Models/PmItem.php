<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'pm_purchase_id', 'packing_material_id', 'qty', 'unit_cost', 'total', 'date', 'action',
    ];

    public function material()
    {
        return $this->belongsTo(PackingMaterial::class);
    }
}
