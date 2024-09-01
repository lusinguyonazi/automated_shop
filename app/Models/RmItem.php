<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'rm_purchase_id', 'raw_material_id', 'qty', 'unit_cost', 'total', 'date', 'action',
    ];

    public function material()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
