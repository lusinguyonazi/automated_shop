<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MroUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'total_cost', 'date' , 'prod_batch', 
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function mroItems()
    {
        return $this->hasMany(MroItem::class);
    }

    public function user(){

        return $this->belongsTo(User::class , 'user_id' , 'id');
    }
}
