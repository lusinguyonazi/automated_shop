<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mro extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'name',
    ];

    public function shop()
    {
        return $this->belongsTo(shop::class);
    }

    public function mroItems()
    {
        return $this->hasMany(MroItem::class);
    }
}
