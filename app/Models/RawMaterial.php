<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
         'name', 'basic_unit', 'type',
    ];

 

    public function rmItems()
    {
        return $this->hasMany(RmItem::class);
    }

    public function rmusedItems(){
        return $this->hasMany(rmusedItem::class);
    }
}
