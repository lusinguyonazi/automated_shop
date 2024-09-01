<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmUseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'rm_use_id', 'rm_material_id', 'unit_cost', 'total', 'date',
    ];

    public function rmUse()
    {
        return $this->belongsTo(RmUse::class);
    }

    public function material()
    {
        return $this->belongsTo(RawMaterial::class , 'raw_material_id' , 'id');
    }
}
