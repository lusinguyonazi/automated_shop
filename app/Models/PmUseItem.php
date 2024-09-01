<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmUseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'pm_use_id', 'packing_material_id', 'unit_cost', 'total', 'date',
    ];

    public function pmUse()
    {
        return $this->belongsTo(PmUse::class);
    }
}
