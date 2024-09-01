<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MroItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'mro_id', 'mro_use_id' ,'qty', 'unit_cost', 'total', 'date', 'action',
    ];

    public function mro()
    {
        return $this->belongsTo(Mro::class);
    }

    public function mrouse(){
        return $this->belongsTo(MroUse::class);
    }
}
